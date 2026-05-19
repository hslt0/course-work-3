<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Progress;
use JetBrains\PhpStorm\NoReturn;

class UserController extends Controller
{
    public function dashboard(): void
    {
        // Must be logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = User::findById($userId);

        // Fetch courses the user is enrolled in
        $enrolledCourses = Course::getEnrolledByUser($userId);

        // Fetch test results
        $testResults = Progress::getUserTestResults($userId);

        // Fetch completed lesson IDs grouped by course
        $completedLessonsByCourse = [];
        foreach ($enrolledCourses as $course) {
            $completedLessonsByCourse[$course->id] = Progress::getCompletedLessonIdsForCourse($userId, $course->id);
        }

        $this->view('user/dashboard', [
            'user' => $user,
            'courses' => $enrolledCourses,
            'testResults' => $testResults,
            'completedLessonsByCourse' => $completedLessonsByCourse,
            'title' => 'My Dashboard'
        ]);
    }

    public function users(): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: You do not have permission to view this page.");
        }

        $users = User::getAllUsers();

        $this->view('user/users', [
            'users' => $users,
            'title' => 'Manage Users'
        ]);
    }

    #[NoReturn]
    public function toggle_ban(int $id): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: You do not have permission to perform this action.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = isset($_POST['ban_status']) && $_POST['ban_status'] === '1';
            User::toggleBan($id, $status);
        }
        header('Location: ' . URLROOT . '/user/users');
        exit;
    }
}