<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Test;
use JetBrains\PhpStorm\NoReturn;

class QuestionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function create_question(int $testId): void
    {
        $test = Test::getById($testId);
        if (!$test) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Add Question to ' . $test->title,
            'test_id' => $testId,
            'question_id' => null,
            'question_text' => '',
            'answers' => ['', '', '', ''],
            'correct_answer' => '0',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->processQuestionData($data);

            if (empty($data['error'])) {
                $questionId = Question::create($testId, $data['question_text']);
                
                if ($questionId) {
                    $this->extracted($data, $questionId, $testId);
                } else {
                    $data['error'] = 'Something went wrong saving the question.';
                }
            }
        }

        $this->view('question/question_form', $data);
    }

    public function edit_question(int $id): void
    {
        $question = Question::getById($id);
        if (!$question) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $testId = $question->test_id;
        
        $answersData = Answer::getForQuestion($id);
        
        $answersArray = ['', '', '', ''];
        $correctAnswerIndex = '0';
        
        foreach ($answersData as $i => $ans) {
            if ($i < 4) {
                $answersArray[$i] = $ans->answer_text;
                if ($ans->is_correct) {
                    $correctAnswerIndex = (string)$i;
                }
            }
        }

        $data = [
            'title' => 'Edit Question',
            'test_id' => $testId,
            'question_id' => $id,
            'question_text' => $question->question_text,
            'answers' => $answersArray,
            'correct_answer' => $correctAnswerIndex,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->processQuestionData($data);

            if (empty($data['error'])) {
                if (Question::update($id, $data['question_text'])) {
                    Answer::deleteByQuestionId($id);

                    $this->extracted($data, $id, $testId);
                } else {
                    $data['error'] = 'Something went wrong saving the question.';
                }
            }
        }

        $this->view('question/question_form', $data);
    }

    #[NoReturn]
    public function delete_question(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question = Question::getById($id); 
            $testId = $_POST['test_id'] ?? null;
            
            if ($testId && $question) {
                Question::delete($id);
                header('Location: ' . URLROOT . '/tests/manage_test/' . $testId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    #[NoReturn]
    private function extracted(array $data, int $id, int $testId): void
    {
        foreach ($data['answers'] as $index => $ansText) {
            if (!empty($ansText)) {
                $isCorrect = ((int)$data['correct_answer'] === $index) ? 1 : 0;
                Answer::create($id, $ansText, $isCorrect);
            }
        }
        header('Location: ' . URLROOT . '/tests/manage_test/' . $testId);
        exit;
    }

    private function processQuestionData(array $data): array
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data['question_text'] = trim($_POST['question_text']);
        $data['answers'] = [
            trim($_POST['answer_0'] ?? ''),
            trim($_POST['answer_1'] ?? ''),
            trim($_POST['answer_2'] ?? ''),
            trim($_POST['answer_3'] ?? '')
        ];
        $data['correct_answer'] = $_POST['correct_answer'] ?? '0';

        $validAnswersCount = 0;
        foreach ($data['answers'] as $ans) {
            if (!empty($ans)) $validAnswersCount++;
        }

        if (empty($data['question_text'])) {
            $data['error'] = 'Question text is required.';
        } elseif ($validAnswersCount < 2) {
            $data['error'] = 'Please provide at least 2 answers.';
        } elseif (empty($data['answers'][(int)$data['correct_answer']])) {
            $data['error'] = 'The selected correct answer cannot be empty.';
        }

        return $data;
    }
}