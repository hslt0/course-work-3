<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
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