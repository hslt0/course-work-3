<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Comment;
use JetBrains\PhpStorm\NoReturn;

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

        if (!isset($_SESSION['user_id']) || !Enrollment::isEnrolled($_SESSION['user_id'], $lesson->course_id)) {
            header('Location: ' . URLROOT . '/courses/show/' . $lesson->course_id);
            exit;
        }

        $course = Course::getById($lesson->course_id);
        $isCompleted = Progress::isLessonCompleted($_SESSION['user_id'], $id);

        $prevLesson = Lesson::getPreviousLesson($lesson->course_id, $lesson->order_index);
        $nextLesson = Lesson::getNextLesson($lesson->course_id, $lesson->order_index);
        
        $comments = Comment::getForLesson($id);

        $data = [
            'lesson' => $lesson,
            'course' => $course,
            'isCompleted' => $isCompleted,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'comments' => $comments,
            'title' => $lesson->title
        ];

        $viewName = 'lessons/' . $lesson->type;

        if ($lesson->type === 'ppt' || $lesson->type === 'pptx') {
            $viewName = 'lessons/downloadable';
        }

        $this->view($viewName, $data);
    }

    #[NoReturn]
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
            
            $nextLesson = Lesson::getNextLesson($lesson->course_id, $lesson->order_index);
            if ($nextLesson) {
                header('Location: ' . URLROOT . '/lessons/show/' . $nextLesson->id);
            } else {
                header('Location: ' . URLROOT . '/courses/show/' . $lesson->course_id);
            }
            exit;
        }
        
        header('Location: ' . URLROOT);
        exit;
    }

    #[NoReturn]
    public function comment(int $id): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $commentText = trim($_POST['comment'] ?? '');
            
            if (!empty($commentText)) {
                $lesson = Lesson::getById($id);
                if ($lesson && Enrollment::isEnrolled($_SESSION['user_id'], $lesson->course_id)) {
                    Comment::create($_SESSION['user_id'], $id, $commentText);
                }
            }
        }
        
        header('Location: ' . URLROOT . '/lessons/show/' . $id . '#discussion');
        exit;
    }

    #[NoReturn]
    public function delete_comment(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessonId = $_POST['lesson_id'] ?? null;
            Comment::delete($id);

            if ($lessonId) {
                header('Location: ' . URLROOT . '/lessons/show/' . $lessonId . '#discussion');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    public function create_lesson(int $courseId): void
    {
        $this->requireAdmin();

        $course = Course::getById($courseId);
        if (!$course) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $data = [
            'title' => 'Create Lesson for ' . $course->name,
            'course_id' => $courseId,
            'lesson_id' => null,
            'lesson_title' => '',
            'type' => 'video',
            'content_path' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['lesson_title'] = trim($_POST['lesson_title']);
            $contentSource = $_POST['content_source'] ?? 'path';

            $contentPath = '';
            $lessonType = '';

            if ($contentSource === 'upload') {
                $uploadResult = $this->handleFileUpload($_FILES['lesson_file'] ?? null);
                if ($uploadResult) {
                    $contentPath = $uploadResult['path'];
                    $lessonType = $uploadResult['type'];
                } else {
                    $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                } else {
                    $lessonType = $this->determineTypeFromUrl($contentPath);
                    if (!$lessonType) {
                        $data['error'] = 'Could not determine file type from URL.';
                    }
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($lessonType)) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::create($courseId, $data['lesson_title'], $lessonType, $contentPath)) {
                        header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                        exit;
                    } else {
                        $data['error'] = 'Something went wrong creating the lesson.';
                    }
                }
            }
            if (!empty($contentPath)) {
                $data['content_path'] = $contentPath;
            }
            $data['type'] = $lessonType;
        }

        $this->view('lessons/lesson_form', $data);
    }

    public function edit_lesson(int $id): void
    {
        $this->requireAdmin();

        $lesson = Lesson::getById($id);

        if (!$lesson) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $courseId = $lesson->course_id;

        $data = [
            'title' => 'Edit Lesson',
            'course_id' => $courseId,
            'lesson_id' => $lesson->id,
            'lesson_title' => $lesson->title,
            'type' => $lesson->type,
            'content_path' => $lesson->content_path,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['lesson_title'] = trim($_POST['lesson_title']);
            $contentSource = $_POST['content_source'] ?? 'path';

            $contentPath = $lesson->content_path;
            $lessonType = $lesson->type;

            if ($contentSource === 'upload') {
                $uploadResult = $this->handleFileUpload($_FILES['lesson_file'] ?? null, $lesson->content_path);
                if ($uploadResult) {
                    $contentPath = $uploadResult['path'];
                    $lessonType = $uploadResult['type'];
                } else {
                    $fileInfo = $_FILES['lesson_file'] ?? null;
                    if ($fileInfo && $fileInfo['error'] !== UPLOAD_ERR_NO_FILE) {
                        $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                    }
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                } else {
                    $lessonType = $this->determineTypeFromUrl($contentPath) ?: $lessonType;
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($lessonType)) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::update($id, $data['lesson_title'], $lessonType, $contentPath)) {
                        header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                        exit;
                    } else {
                        $data['error'] = 'Something went wrong updating the lesson.';
                    }
                }
            }

            $data['content_path'] = $contentPath;
            $data['type'] = $lessonType;
        }

        $this->view('lessons/lesson_form', $data);
    }

    #[NoReturn]
    public function delete_lesson(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lesson = Lesson::getById($id);
            if ($lesson) {
                $courseId = $lesson->course_id;
                Lesson::delete($id);
                header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    private function handleFileUpload(?array $fileInfo, ?string $existingPath = null): ?array
    {
        if (!$fileInfo || $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileInfo['tmp_name']);
        finfo_close($finfo);

        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));

        $mimeToType = [
            'application/pdf' => 'pdf',
            'video/mp4' => 'video',
            'text/plain' => 'markdown',
            'text/markdown' => 'markdown',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.ms-powerpoint' => 'ppt'
        ];

        $lessonType = $mimeToType[$mimeType] ?? null;

        if (!$lessonType) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/lessons/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
            return [
                'path' => '/uploads/lessons/' . $fileName,
                'type' => $lessonType
            ];
        }

        return null;
    }

    private function determineTypeFromUrl(string $url): string
    {
        $url = strtolower($url);
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be') || str_ends_with($url, '.mp4')) {
            return 'video';
        }
        if (str_ends_with($url, '.pdf')) {
            return 'pdf';
        }
        if (str_ends_with($url, '.md') || str_ends_with($url, '.txt')) {
            return 'markdown';
        }
        if (str_ends_with($url, '.ppt') || str_ends_with($url, '.pptx')) {
            return 'pptx';
        }
        return '';
    }
}