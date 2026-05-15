<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;

class CoursesController extends Controller
{
    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $language = $_GET['language'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';
        $sortBy = $_GET['sort'] ?? '';

        $courses = Course::searchAndFilter($search, $language, $difficulty, $sortBy);
        $languages = Course::getDistinctLanguages();

        $this->view('courses/index', [
            'courses' => $courses,
            'languages' => $languages,
            'filters' => [
                'search' => $search,
                'language' => $language,
                'difficulty' => $difficulty,
                'sort' => $sortBy
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

        $this->view('courses/show', [
            'course' => $course,
            'lessons' => $lessons,
            'tests' => $tests,
            'title' => $course->name
        ]);
    }
}