<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;

class CoursesController extends Controller
{
    public function index(): void
    {
        $courses = Course::getAll();

        $this->view('courses/index', [
            'courses' => $courses,
            'title' => 'All Courses'
        ]);
    }
}
