<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lesson;
use App\Models\Course; // To get course context

class LessonsController extends Controller
{
    public function show(int $id): void
    {
        $lesson = Lesson::getById($id);

        if (!$lesson) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Lesson Not Found']);
            return;
        }

        $course = Course::getById($lesson->course_id);

        $data = [
            'lesson' => $lesson,
            'course' => $course,
            'title' => $lesson->title
        ];

        // Choose the view based on the lesson type
        $viewName = 'lessons/' . $lesson->type;

        // For ppt/pptx, we can just offer a download link in a generic view
        if ($lesson->type === 'ppt' || $lesson->type === 'pptx') {
            $viewName = 'lessons/downloadable';
        }

        $this->view($viewName, $data);
    }
}
