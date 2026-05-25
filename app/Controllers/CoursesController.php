<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Paginator;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\Enrollment;
use App\Models\Review;
use JetBrains\PhpStorm\NoReturn;

class CoursesController extends Controller
{
    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $language = $_GET['language'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';
        $sortBy = $_GET['sort'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $itemsPerPage = 6;
        
        $enrolledFilter = '';
        if (isset($_SESSION['user_id']) && isset($_GET['enrolled'])) {
            $enrolledFilter = $_GET['enrolled'];
        }
        
        $userId = $_SESSION['user_id'] ?? null;

        $offset = max(0, ($page - 1) * $itemsPerPage);

        $result = Course::searchAndFilterPaginated(
            $search, 
            $language, 
            $difficulty, 
            $sortBy, 
            $enrolledFilter, 
            $userId, 
            $itemsPerPage, 
            $offset
        );

        $courses = $result['data'];
        $totalItems = $result['total'];

        $paginator = new Paginator($totalItems, $itemsPerPage, $page);
        
        $languages = Course::getDistinctLanguages();

        $this->view('courses/index', [
            'courses' => $courses,
            'paginator' => $paginator,
            'languages' => $languages,
            'filters' => [
                'search' => $search,
                'language' => $language,
                'difficulty' => $difficulty,
                'sort' => $sortBy,
                'enrolled' => $enrolledFilter
            ],
            'title' => 'Our Courses'
        ]);
    }

    public function show(int $id): void
    {
        $course = Course::getById($id);

        if (!$course) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Course Not Found']);
            return;
        }

        $lessons = Lesson::getForCourse($id);
        $tests = Test::getForCourse($id);
        $reviews = Review::getForCourse($id);
        $averageRating = Review::getAverageRatingForCourse($id);

        $isEnrolled = false;
        $hasReviewed = false;
        if (isset($_SESSION['user_id'])) {
            $isEnrolled = Enrollment::isEnrolled($_SESSION['user_id'], $id);
            $hasReviewed = Review::hasUserReviewed($_SESSION['user_id'], $id);
        }

        $this->view('courses/show', [
            'course' => $course,
            'lessons' => $lessons,
            'tests' => $tests,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'isEnrolled' => $isEnrolled,
            'hasReviewed' => $hasReviewed,
            'title' => $course->name
        ]);
    }

    #[NoReturn]
    public function enroll(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course = Course::getById($id);
            if ($course) {
                Enrollment::enroll($_SESSION['user_id'], $id);
            }
        }

        header('Location: ' . URLROOT . '/courses/show/' . $id);
        exit;
    }

    #[NoReturn]
    public function unenroll(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Enrollment::unenroll($_SESSION['user_id'], $id);
        }

        header('Location: ' . URLROOT . '/courses/show/' . $id);
        exit;
    }

    #[NoReturn]
    public function review(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $rating = (int)($_POST['rating'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            if ($rating >= 1 && $rating <= 5) {
                if (Enrollment::isEnrolled($_SESSION['user_id'], $id) && !Review::hasUserReviewed($_SESSION['user_id'], $id)) {
                    Review::create($_SESSION['user_id'], $id, $rating, empty($comment) ? null : $comment);
                }
            }
        }

        header('Location: ' . URLROOT . '/courses/show/' . $id . '#reviews');
        exit;
    }

    public function create_course(): void
    {
        $this->requireAdmin();

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
            $data = $this->getData($data);

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

        $this->view('courses/course_form', $data);
    }

    public function edit_course(int $id): void
    {
        $this->requireAdmin();

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
            $data = $this->getData($data);

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

        $this->view('courses/course_form', $data);
    }

    #[NoReturn]
    public function delete_course(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Course::delete($id);
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    public function manage_course(int $id): void
    {
        $this->requireAdmin();

        $course = Course::getById($id);

        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $lessons = Lesson::getForCourse($id);
        $tests = Test::getForCourse($id);

        $this->view('courses/manage_course', [
            'course' => $course,
            'lessons' => $lessons,
            'tests' => $tests,
            'title' => 'Manage Content: ' . $course->name
        ]);
    }

    private function getData(array $data): array
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data['name'] = trim($_POST['name']);
        $data['language'] = trim($_POST['language']);
        $data['difficulty'] = trim($_POST['difficulty']);
        $data['description'] = trim($_POST['description']);
        $data['preview_image'] = trim($_POST['preview_image']);
        return $data;
    }
}