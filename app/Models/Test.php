<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Test
{
    public int $id;
    public int $course_id;
    public string $title;

    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM tests WHERE course_id = :course_id');
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM tests WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $test = $stmt->fetchObject(self::class);
        return $test ?: null;
    }

    public static function create(int $courseId, string $title): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO tests (course_id, title) VALUES (:course_id, :title)');
        return $stmt->execute([
            'course_id' => $courseId,
            'title' => $title
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM tests WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}