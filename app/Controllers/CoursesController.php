<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\Enrollment;
use App\Models\Review;

class CoursesController extends Controller
{
    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $language = $_GET['language'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';
        $sortBy = $_GET['sort'] ?? '';
        
        // Only consider the enrollment filter if the user is logged in
        $enrolledFilter = '';
        if (isset($_SESSION['user_id']) && isset($_GET['enrolled'])) {
            $enrolledFilter = $_GET['enrolled']; // e.g. 'yes' or 'no'
        }
        
        $userId = $_SESSION['user_id'] ?? null;

        $courses = Course::searchAndFilter($search, $language, $difficulty, $sortBy, $enrolledFilter, $userId);
        $languages = Course::getDistinctLanguages();

        $this->view('courses/index', [
            'courses' => $courses,
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

    public function enroll(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            // Must be logged in to enroll
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

    public function unenroll(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Enrollment::unenroll($_SESSION['user_id'], $id);
        }

        // Redirect back to the course or to their dashboard
        header('Location: ' . URLROOT . '/courses/show/' . $id);
        exit;
    }

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

            // Basic validation
            if ($rating >= 1 && $rating <= 5) {
                // Must be enrolled to review, and can only review once
                if (Enrollment::isEnrolled($_SESSION['user_id'], $id) && !Review::hasUserReviewed($_SESSION['user_id'], $id)) {
                    Review::create($_SESSION['user_id'], $id, $rating, empty($comment) ? null : $comment);
                }
            }
        }

        header('Location: ' . URLROOT . '/courses/show/' . $id . '#reviews');
        exit;
    }
}
