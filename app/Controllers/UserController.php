<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\User;

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

        $this->view('user/dashboard', [
            'user' => $user,
            'courses' => $enrolledCourses,
            'title' => 'My Dashboard'
        ]);
    }
}
