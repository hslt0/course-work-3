<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;

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
}