<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;

class AdminController extends Controller
{
    public function __construct()
    {
        // Protect all admin routes
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: You do not have permission to view this page.");
        }
    }

    public function dashboard(): void
    {
        $courses = Course::getAll();
        
        $this->view('admin/dashboard', [
            'courses' => $courses,
            'title' => 'Admin Dashboard'
        ]);
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
            'lesson_title' => '',
            'type' => 'video',
            'content_path' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['lesson_title'] = trim($_POST['lesson_title']);
            $data['type'] = trim($_POST['type']);
            $data['content_path'] = trim($_POST['content_path']); // Assuming URL or path input for now

            if (empty($data['lesson_title']) || empty($data['type']) || empty($data['content_path'])) {
                $data['error'] = 'Please fill out all required fields.';
            } else {
                if (Lesson::create($courseId, $data['lesson_title'], $data['type'], $data['content_path'])) {
                    header('Location: ' . URLROOT . '/admin/manage_course/' . $courseId);
                    exit;
                } else {
                    $data['error'] = 'Something went wrong creating the lesson.';
                }
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
}
