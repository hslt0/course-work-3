<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Progress
{
    /**
     * Mark a lesson as completed for a user.
     */
    public static function markLessonComplete(int $userId, int $lessonId): bool
    {
        $db = Database::getInstance();
        
        // Use INSERT IGNORE or ON CONFLICT so it won't fail if they already completed it
        $stmt = $db->prepare('INSERT INTO completed_lessons (user_id, lesson_id) VALUES (:user_id, :lesson_id) ON CONFLICT(user_id, lesson_id) DO NOTHING');
        return $stmt->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
    }

    /**
     * Check if a specific lesson is completed by a user.
     */
    public static function isLessonCompleted(int $userId, int $lessonId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM completed_lessons WHERE user_id = :user_id AND lesson_id = :lesson_id');
        $stmt->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Get an array of completed lesson IDs for a specific user and course.
     */
    public static function getCompletedLessonIdsForCourse(int $userId, int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT cl.lesson_id 
            FROM completed_lessons cl
            JOIN lessons l ON cl.lesson_id = l.id
            WHERE cl.user_id = :user_id AND l.course_id = :course_id
        ');
        $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Save a test result.
     */
    public static function saveTestResult(int $userId, int $testId, int $score, int $total): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO test_results (user_id, test_id, score, total) VALUES (:user_id, :test_id, :score, :total)');
        return $stmt->execute([
            'user_id' => $userId,
            'test_id' => $testId,
            'score' => $score,
            'total' => $total
        ]);
    }

    /**
     * Get all test results for a user.
     */
    public static function getUserTestResults(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT tr.*, t.title as test_title, c.name as course_name 
            FROM test_results tr
            JOIN tests t ON tr.test_id = t.id
            JOIN courses c ON t.course_id = c.id
            WHERE tr.user_id = :user_id
            ORDER BY tr.taken_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
