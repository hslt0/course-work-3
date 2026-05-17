<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;

class TestsController extends Controller
{
    /**
     * Display the test for the user to take.
     */
    public function show(int $id): void
    {
        $test = Test::getById($id);

        if (!$test) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Test Not Found']);
            return;
        }

        // Check if user is logged in and enrolled in the parent course
        if (!isset($_SESSION['user_id']) || !Enrollment::isEnrolled($_SESSION['user_id'], $test->course_id)) {
            // Redirect to course page if not enrolled
            header('Location: ' . URLROOT . '/courses/show/' . $test->course_id);
            exit;
        }

        $course = Course::getById($test->course_id);
        $questions = Question::getForTest($id);
        
        // Prepare questions with their respective answers
        $questionsWithAnswers = [];
        foreach ($questions as $question) {
            $questionsWithAnswers[] = [
                'question' => $question,
                'answers' => Answer::getForQuestion($question->id)
            ];
        }

        $this->view('tests/show', [
            'test' => $test,
            'course' => $course,
            'questionsWithAnswers' => $questionsWithAnswers,
            'title' => $test->title
        ]);
    }

    /**
     * Process the submitted test.
     */
    public function submit(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/tests/show/' . $id);
            exit;
        }

        $test = Test::getById($id);
        if (!$test) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Test Not Found']);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;

        // Security check: Must be enrolled to submit
        if (!$userId || !Enrollment::isEnrolled($userId, $test->course_id)) {
            header('Location: ' . URLROOT . '/courses/show/' . $test->course_id);
            exit;
        }

        $course = Course::getById($test->course_id);
        $questions = Question::getForTest($id);
        
        $score = 0;
        $totalQuestions = count($questions);
        $results = [];

        foreach ($questions as $question) {
            $submittedAnswerId = $_POST['question_' . $question->id] ?? null;
            $answers = Answer::getForQuestion($question->id);
            
            $correctAnswerId = null;
            $userAnswerCorrect = false;

            foreach ($answers as $answer) {
                if ($answer->is_correct) {
                    $correctAnswerId = $answer->id;
                }
            }

            if ($submittedAnswerId && (int)$submittedAnswerId === $correctAnswerId) {
                $score++;
                $userAnswerCorrect = true;
            }

            $results[] = [
                'question' => $question,
                'answers' => $answers,
                'submitted_answer_id' => $submittedAnswerId ? (int)$submittedAnswerId : null,
                'correct_answer_id' => $correctAnswerId,
                'is_correct' => $userAnswerCorrect
            ];
        }

        // Save the test result to the database
        Progress::saveTestResult($userId, $id, $score, $totalQuestions);

        $this->view('tests/results', [
            'test' => $test,
            'course' => $course,
            'score' => $score,
            'total' => $totalQuestions,
            'results' => $results,
            'title' => $test->title . ' - Results'
        ]);
    }
}