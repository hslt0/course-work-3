<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Lesson
{
    public int $id;
    public int $course_id;
    public int $order_index;
    public string $title;
    public string $type;
    public string $content_path;

    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE course_id = :course_id ORDER BY order_index , id ');
        $stmt->execute(['course_id' => $courseId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        $lesson = $stmt->fetchObject(self::class);
        return $lesson ?: null;
    }

    public static function getPreviousLesson(int $courseId, int $currentOrderIndex): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE course_id = :course_id AND order_index < :order_index ORDER BY order_index DESC LIMIT 1');
        $stmt->execute(['course_id' => $courseId, 'order_index' => $currentOrderIndex]);
        $lesson = $stmt->fetchObject(self::class);
        return $lesson ?: null;
    }

    public static function getNextLesson(int $courseId, int $currentOrderIndex): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM lessons WHERE course_id = :course_id AND order_index > :order_index ORDER BY order_index LIMIT 1');
        $stmt->execute(['course_id' => $courseId, 'order_index' => $currentOrderIndex]);
        $lesson = $stmt->fetchObject(self::class);
        return $lesson ?: null;
    }

    public static function create(int $courseId, string $title, string $type, string $contentPath): bool
    {
        $db = Database::getInstance();
        
        $orderStmt = $db->prepare('SELECT MAX(order_index) FROM lessons WHERE course_id = :course_id');
        $orderStmt->execute(['course_id' => $courseId]);
        $maxOrder = (int)$orderStmt->fetchColumn();
        $newOrder = $maxOrder + 1;

        $stmt = $db->prepare('INSERT INTO lessons (course_id, order_index, title, type, content_path) VALUES (:course_id, :order_index, :title, :type, :content_path)');
        return $stmt->execute([
            'course_id' => $courseId,
            'order_index' => $newOrder,
            'title' => $title,
            'type' => $type,
            'content_path' => $contentPath
        ]);
    }

    public static function update(int $id, string $title, string $type, string $contentPath): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE lessons SET title = :title, type = :type, content_path = :content_path WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'content_path' => $contentPath
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM lessons WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}