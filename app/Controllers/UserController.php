<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Progress;

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
}