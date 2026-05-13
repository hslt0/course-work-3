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
     *
     * @param int $courseId
     * @return self[]
     */
    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        // Use a prepared statement to prevent SQL injection
        $stmt = $db->prepare('SELECT * FROM lessons WHERE course_id = :course_id ORDER BY id');
        $stmt->execute(['course_id' => $courseId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
