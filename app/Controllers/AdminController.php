<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Comment;

class AdminController extends Controller
{
    public function __construct()
    {
        // Protect all admin routes
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: You do not have permission to view this page.");
        }

        parent::__construct();
    }

    public function dashboard(): void
    {
        $courses = Course::getAll();
        
        $this->view('admin/dashboard', [
            'courses' => $courses,
            'title' => 'Admin Dashboard'
        ]);
    }

    public function users(): void
    {
        $users = User::getAllUsers();
        
        $this->view('admin/users', [
            'users' => $users,
            'title' => 'Manage Users'
        ]);
    }

    public function toggle_ban(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = isset($_POST['ban_status']) && $_POST['ban_status'] === '1';
            User::toggleBan($id, $status);
        }
        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }

    public function delete_comment(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessonId = $_POST['lesson_id'] ?? null;
            Comment::delete($id);
            
            if ($lessonId) {
                header('Location: ' . URLROOT . '/lessons/show/' . $lessonId . '#discussion');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    public function create_course(): void
    {
        $data = [
            'title' => 'Create Course',
            'name' => '',
            'language' => '',
            'difficulty' => 'Beginner',
            'description' => '',
            'preview_image' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['name'] = trim($_POST['name']);
            $data['language'] = trim($_POST['language']);
            $data['difficulty'] = trim($_POST['difficulty']);
            $data['description'] = trim($_POST['description']);
            $data['preview_image'] = trim($_POST['preview_image']);

            if (empty($data['name']) || empty($data['language']) || empty($data['description'])) {
                $data['error'] = 'Please fill out all required fields.';
            } else {
                if (Course::create($data['name'], $data['language'], $data['difficulty'], $data['description'], $data['preview_image'] ?: null)) {
                    header('Location: ' . URLROOT . '/admin/dashboard');
                    exit;
                } else {
                    $data['error'] = 'Something went wrong creating the course.';
                }
            }
        }

        $this->view('admin/course_form', $data);
    }

    public function edit_course(int $id): void
    {
        $course = Course::getById($id);

        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Edit Course',
            'id' => $course->id,
            'name' => $course->name,
            'language' => $course->language,
            'difficulty' => $course->difficulty_level,
            'description' => $course->description,
            'preview_image' => $course->preview_image ?? '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['name'] = trim($_POST['name']);
            $data['language'] = trim($_POST['language']);
            $data['difficulty'] = trim($_POST['difficulty']);
            $data['description'] = trim($_POST['description']);
            $data['preview_image'] = trim($_POST['preview_image']);

            if (empty($data['name']) || empty($data['language']) || empty($data['description'])) {
                $data['error'] = 'Please fill out all required fields.';
            } else {
                if (Course::update($id, $data['name'], $data['language'], $data['difficulty'], $data['description'], $data['preview_image'] ?: null)) {
                    header('Location: ' . URLROOT . '/admin/dashboard');
                    exit;
                } else {
                    $data['error'] = 'Something went wrong updating the course.';
                }
            }
        }

        $this->view('admin/course_form', $data);
    }

    public function delete_course(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Course::delete($id);
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    public function manage_course(int $id): void
    {
        $course = Course::getById($id);
        
        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $lessons = Lesson::getForCourse($id);
        $tests = Test::getForCourse($id);

        $this->view('admin/manage_course', [
            'course' => $course,
            'lessons' => $lessons,
            'tests' => $tests,
            'title' => 'Manage Content: ' . $course->name
        ]);
    }

    // --- LESSON MANAGEMENT ---

    private function handleFileUpload(?array $fileInfo, ?string $existingPath = null): ?string
    {
        if (!$fileInfo || $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingPath; 
        }

        if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
            return null; 
        }

        // Hardening: Verify MIME type to prevent malicious uploads (e.g. PHP disguised as PDF)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileInfo['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'application/pdf', 
            'video/mp4', 
            'text/plain', 
            'text/markdown',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
            'application/vnd.ms-powerpoint' // ppt
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return null; // Invalid file type
        }

        // Hardening: Strictly validate file extension
        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'mp4', 'txt', 'md', 'pptx', 'ppt'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/lessons/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a safe, unique filename
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
            return '/uploads/lessons/' . $fileName; 
        }

        return null;
    }

    public function create_lesson(int $courseId): void
    {
        $course = Course::getById($courseId);
        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Create Lesson for ' . $course->name,
            'course_id' => $courseId,
            'lesson_id' => null,
            'lesson_title' => '',
            'type' => 'video',
            'content_path' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['lesson_title'] = trim($_POST['lesson_title']);
            $data['type'] = trim($_POST['type']);
            $contentSource = $_POST['content_source'] ?? 'path';
            
            $contentPath = '';
            
            if ($contentSource === 'upload') {
                $uploadedPath = $this->handleFileUpload($_FILES['lesson_file'] ?? null);
                if ($uploadedPath) {
                    $contentPath = $uploadedPath;
                } else {
                    $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($data['type'])) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::create($courseId, $data['lesson_title'], $data['type'], $contentPath)) {
                        header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                        exit;
                    } else {
                        $data['error'] = 'Something went wrong creating the lesson.';
                    }
                }
            }
            if (empty($data['content_path']) && !empty($contentPath)) {
                $data['content_path'] = $contentPath;
            }
        }

        $this->view('admin/lesson_form', $data);
    }

    public function edit_lesson(int $id): void
    {
        $lesson = Lesson::getById($id);
        
        if (!$lesson) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $courseId = $lesson->course_id;

        $data = [
            'title' => 'Edit Lesson',
            'course_id' => $courseId,
            'lesson_id' => $lesson->id,
            'lesson_title' => $lesson->title,
            'type' => $lesson->type,
            'content_path' => $lesson->content_path,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['lesson_title'] = trim($_POST['lesson_title']);
            $data['type'] = trim($_POST['type']);
            $contentSource = $_POST['content_source'] ?? 'path';
            
            $contentPath = '';
            
            if ($contentSource === 'upload') {
                $uploadedPath = $this->handleFileUpload($_FILES['lesson_file'] ?? null, $lesson->content_path);
                if ($uploadedPath) {
                    $contentPath = $uploadedPath;
                } else {
                    $fileInfo = $_FILES['lesson_file'] ?? null;
                    if ($fileInfo && $fileInfo['error'] !== UPLOAD_ERR_NO_FILE) {
                         $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                    } else {
                        $contentPath = $lesson->content_path;
                    }
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($data['type'])) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::update($id, $data['lesson_title'], $data['type'], $contentPath)) {
                        header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                        exit;
                    } else {
                        $data['error'] = 'Something went wrong updating the lesson.';
                    }
                }
            }
            
            if (empty($data['content_path']) && !empty($contentPath)) {
                 $data['content_path'] = $contentPath;
            }
        }

        $this->view('admin/lesson_form', $data);
    }

    public function delete_lesson(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lesson = Lesson::getById($id);
            if ($lesson) {
                $courseId = $lesson->course_id;
                Lesson::delete($id);
                header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    // --- TEST MANAGEMENT ---

    public function manage_test(int $id): void
    {
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

        $this->view('admin/manage_test', [
            'test' => $test,
            'course' => $course,
            'questionsWithAnswers' => $questionsWithAnswers,
            'title' => 'Manage Test: ' . $test->title
        ]);
    }

    public function create_test(int $courseId): void
    {
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
                    header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                    exit;
                } else {
                    $data['error'] = 'Something went wrong creating the test.';
                }
            }
        }

        $this->view('admin/test_form', $data);
    }

    public function delete_test(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $test = Test::getById($id);
            if ($test) {
                $courseId = $test->course_id;
                Test::delete($id);
                header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
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
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['question_text'] = trim($_POST['question_text']);
            $data['answers'] = [
                trim($_POST['answer_0'] ?? ''),
                trim($_POST['answer_1'] ?? ''),
                trim($_POST['answer_2'] ?? ''),
                trim($_POST['answer_3'] ?? '')
            ];
            $data['correct_answer'] = $_POST['correct_answer'] ?? '0';

            // Validate
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
            } else {
                // Save question
                $questionId = Question::create($testId, $data['question_text']);
                
                if ($questionId) {
                    // Save answers
                    foreach ($data['answers'] as $index => $ansText) {
                        if (!empty($ansText)) {
                            $isCorrect = ((int)$data['correct_answer'] === $index) ? 1 : 0;
                            Answer::create($questionId, $ansText, $isCorrect);
                        }
                    }
                    header('Location: ' . URLROOT . '/admin/manage_test/' . $testId);
                    exit;
                } else {
                    $data['error'] = 'Something went wrong saving the question.';
                }
            }
        }

        $this->view('admin/question_form', $data);
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
        
        // Prepare data for form
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
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['question_text'] = trim($_POST['question_text']);
            $data['answers'] = [
                trim($_POST['answer_0'] ?? ''),
                trim($_POST['answer_1'] ?? ''),
                trim($_POST['answer_2'] ?? ''),
                trim($_POST['answer_3'] ?? '')
            ];
            $data['correct_answer'] = $_POST['correct_answer'] ?? '0';

            // Validate
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
            } else {
                // Update question
                if (Question::update($id, $data['question_text'])) {
                    // Update answers: simplest way is delete old ones, recreate new ones
                    Answer::deleteByQuestionId($id);

                    foreach ($data['answers'] as $index => $ansText) {
                        if (!empty($ansText)) {
                            $isCorrect = ((int)$data['correct_answer'] === $index) ? 1 : 0;
                            Answer::create($id, $ansText, $isCorrect);
                        }
                    }
                    header('Location: ' . URLROOT . '/admin/manage_test/' . $testId);
                    exit;
                } else {
                    $data['error'] = 'Something went wrong saving the question.';
                }
            }
        }

        $this->view('admin/question_form', $data);
    }

    public function delete_question(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question = Question::getById($id); 
            $testId = $_POST['test_id'] ?? null;
            
            if ($testId && $question) {
                Question::delete($id);
                header('Location: ' . URLROOT . '/admin/manage_test/' . $testId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }
}