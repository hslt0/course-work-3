<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;

class CoursesController extends Controller
{
    public function index(): void
    {
        $courses = Course::getAll();

        $this->view('courses/index', [
            'courses' => $courses,
            'title' => 'All courses'
        ]);
    }

    public function show(int $id): void
    {
        $course = Course::getById($id);

        if (!$course) {
            // A simple 404 handler
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Course Not Found']);
            return;
        }

        $lessons = Lesson::getForCourse($id);

        $this->view('courses/show', [
            'course' => $course,
            'lessons' => $lessons,
            'title' => $course->name
        ]);
    }
}
