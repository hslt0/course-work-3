<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;

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

        // Check if user is logged in and enrolled in the parent course
        if (!isset($_SESSION['user_id']) || !Enrollment::isEnrolled($_SESSION['user_id'], $lesson->course_id)) {
            // Redirect to course page if not enrolled
            header('Location: ' . URLROOT . '/courses/show/' . $lesson->course_id);
            exit;
        }

        $course = Course::getById($lesson->course_id);
        $isCompleted = Progress::isLessonCompleted($_SESSION['user_id'], $id);

        $data = [
            'lesson' => $lesson,
            'course' => $course,
            'isCompleted' => $isCompleted,
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

    public function complete(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lesson = Lesson::getById($id);
            if ($lesson && Enrollment::isEnrolled($_SESSION['user_id'], $lesson->course_id)) {
                Progress::markLessonComplete($_SESSION['user_id'], $id);
            }
            
            // Redirect back to course page after marking complete
            header('Location: ' . URLROOT . '/courses/show/' . $lesson->course_id);
            exit;
        }
        
        header('Location: ' . URLROOT);
        exit;
    }
}