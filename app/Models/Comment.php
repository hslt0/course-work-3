<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Comment
{
    public int $id;
    public int $user_id;
    public int $lesson_id;
    public string $comment_text;
    public string $created_at;
    
    // Virtual property populated by JOIN
    public string $user_name;

    /**
     * Get all comments for a specific lesson, oldest first.
     */
    public static function getForLesson(int $lessonId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT c.*, u.name as user_name 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.lesson_id = :lesson_id
            ORDER BY c.created_at
        ');
        $stmt->execute(['lesson_id' => $lessonId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Add a new comment to a lesson.
     */
    public static function create(int $userId, int $lessonId, string $commentText): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO comments (user_id, lesson_id, comment_text) 
            VALUES (:user_id, :lesson_id, :comment_text)
        ');
        return $stmt->execute([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'comment_text' => $commentText
        ]);
    }
    
    /**
     * Delete a comment (usually only allowed by admin or the comment owner)
     */
    public static function delete(int $id): bool
    {
         $db = Database::getInstance();
         $stmt = $db->prepare('DELETE FROM comments WHERE id = :id');
         return $stmt->execute(['id' => $id]);
    }
}
