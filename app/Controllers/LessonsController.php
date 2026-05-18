<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Comment;

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

        // Fetch prev and next lessons for navigation
        $prevLesson = Lesson::getPreviousLesson($lesson->course_id, $lesson->order_index);
        $nextLesson = Lesson::getNextLesson($lesson->course_id, $lesson->order_index);
        
        // Fetch comments for the discussion section
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
            
            // Redirect to next lesson if it exists, otherwise back to course
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
                // Ensure they are enrolled before allowing comments
                if ($lesson && Enrollment::isEnrolled($_SESSION['user_id'], $lesson->course_id)) {
                    Comment::create($_SESSION['user_id'], $id, $commentText);
                }
            }
        }
        
        header('Location: ' . URLROOT . '/lessons/show/' . $id . '#discussion');
        exit;
    }

    public function delete_comment(int $id): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied");
        }

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
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied");
        }

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
            $data['type'] = trim($_POST['type']);
            $contentSource = $_POST['content_source'] ?? 'path';

            $contentPath = '';

            if ($contentSource === 'upload') {
                $uploadedPath = $this->handleFileUpload($_FILES['lesson_file'] ?? null);
                if ($uploadedPath) {
                    $contentPath = $uploadedPath;
                } else {
                    $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($data['type'])) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::create($courseId, $data['lesson_title'], $data['type'], $contentPath)) {
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
        }

        $this->view('admin/lesson_form', $data);
    }

    public function edit_lesson(int $id): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied");
        }

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
            $data['type'] = trim($_POST['type']);
            $contentSource = $_POST['content_source'] ?? 'path';

            $contentPath = '';

            if ($contentSource === 'upload') {
                $uploadedPath = $this->handleFileUpload($_FILES['lesson_file'] ?? null, $lesson->content_path);
                if ($uploadedPath) {
                    $contentPath = $uploadedPath;
                } else {
                    $fileInfo = $_FILES['lesson_file'] ?? null;
                    if ($fileInfo && $fileInfo['error'] !== UPLOAD_ERR_NO_FILE) {
                        $data['error'] = 'File upload failed. Make sure the file is a valid format (PDF, MP4, Markdown, PPTX) and within size limits.';
                    } else {
                        $contentPath = $lesson->content_path;
                    }
                }
            } else {
                $contentPath = trim($_POST['content_path'] ?? '');
                if (empty($contentPath)) {
                    $data['error'] = 'Please provide a content path or URL.';
                }
            }

            if (empty($data['error'])) {
                if (empty($data['lesson_title']) || empty($data['type'])) {
                    $data['error'] = 'Please fill out all required fields.';
                } else {
                    if (Lesson::update($id, $data['lesson_title'], $data['type'], $contentPath)) {
                        header('Location: ' . URLROOT . '/courses/manage_course/' . $courseId);
                        exit;
                    } else {
                        $data['error'] = 'Something went wrong updating the lesson.';
                    }
                }
            }

            if (empty($data['content_path']) && !empty($contentPath)) {
                $data['content_path'] = $contentPath;
            }
        }

        $this->view('admin/lesson_form', $data);
    }

    public function delete_lesson(int $id): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied");
        }

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

    private function handleFileUpload(?array $fileInfo, ?string $existingPath = null): ?string
    {
        if (!$fileInfo || $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileInfo['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'application/pdf',
            'video/mp4',
            'text/plain',
            'text/markdown',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint'
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return null;
        }

        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'mp4', 'txt', 'md', 'pptx', 'ppt'];

        if (!in_array($extension, $allowedExtensions)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/lessons/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
            return '/uploads/lessons/' . $fileName;
        }

        return null;
    }
}
