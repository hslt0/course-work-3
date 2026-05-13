<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Lesson
{
    public int $id;
    public int $course_id;
    public string $title;
    public string $type; // 'pdf', 'video', 'pptx', 'markdown'
    public string $content_path;

    /**
     * Fetches all lessons for a given course ID.
     */
    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE course_id = :course_id ORDER BY id ASC');
        $stmt->execute(['course_id' => $courseId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Fetches a single lesson by its ID.
     */
    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        $lesson = $stmt->fetchObject(self::class);
        return $lesson ?: null;
    }
}
