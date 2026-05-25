<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\RateLimiter;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use JetBrains\PhpStorm\NoReturn;

class TestsController extends Controller
{
    public function show(int $id): void
    {
        $test = Test::getById($id);

        if (!$test) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Test Not Found']);
            return;
        }

        if (!isset($_SESSION['user_id']) || !Enrollment::isEnrolled($_SESSION['user_id'], $test->course_id)) {
            header('Location: ' . URLROOT . '/courses/show/' . $test->course_id);
            exit;
        }

        $course = Course::getById($test->course_id);
        $questions = Question::getForTest($id);
        
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

    public function submit(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/tests/show/' . $id);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $actionKey = 'submit_test_' . ($userId ?? 'guest');

        if (RateLimiter::check($actionKey, 10, 3600)) {
            http_response_code(429);
            die('Too many submissions. Please wait a while before trying again.');
        }

        CSRF::enforce();

        $test = Test::getById($id);
        if (!$test) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Test Not Found']);
            return;
        }

        if (!$userId || !Enrollment::isEnrolled($userId, $test->course_id)) {
            header('Location: ' . URLROOT . '/courses/show/' . $test->course_id);
            exit;
        }

        RateLimiter::attempt($actionKey, 10, 3600);

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

    public function manage_test(int $id): void
    {
        $this->requireAdmin();

        $test = Test::getById($id);
        if (!$test) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $course = Course::getById($test->course_id);
        $questions = Question::getForTest($id);
        
        $questionsWithAnswers = [];
        foreach ($questions as $question) {
            $questionsWithAnswers[] = [
                'question' => $question,
                'answers' => Answer::getForQuestion($question->id)
            ];
        }

        $this->view('tests/manage_test', [
            'test' => $test,
            'course' => $course,
            'questionsWithAnswers' => $questionsWithAnswers,
            'title' => 'Manage Test: ' . $test->title
        ]);
    }

    public function create_test(int $courseId): void
    {
        $this->requireAdmin();

        $course = Course::getById($courseId);
        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Create Test for ' . $course->name,
            'course_id' => $courseId,
            'test_title' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['test_title'] = trim($_POST['test_title']);

            if (empty($data['test_title'])) {
                $data['error'] = 'Please enter a test title.';
            } else {
                if (Test::create($courseId, $data['test_title'])) {
                    header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                    exit;
                } else {
                    $data['error'] = 'Something went wrong creating the test.';
                }
            }
        }

        $this->view('tests/test_form', $data);
    }

    #[NoReturn]
    public function delete_test(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $test = Test::getById($id);
            if ($test) {
                $courseId = $test->course_id;
                Test::delete($id);
                header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }
}