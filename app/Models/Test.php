<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Test
{
    public int $id;
    public int $course_id;
    public string $title;

    /**
     * Fetches all tests for a given course ID.
     * @return self[]
     */
    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM tests WHERE course_id = :course_id');
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Fetches a single test by its ID.
     */
    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM tests WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $test = $stmt->fetchObject(self::class);
        return $test ?: null;
    }
}
