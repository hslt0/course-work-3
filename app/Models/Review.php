<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Review
{
    public int $id;
    public int $user_id;
    public int $course_id;
    public int $rating;
    public ?string $comment;
    public string $created_at;
    
    // Virtual properties populated by JOIN
    public string $user_name;

    /**
     * Get all reviews for a specific course.
     */
    public static function getForCourse(int $courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT r.*, u.name as user_name 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.course_id = :course_id
            ORDER BY r.created_at DESC
        ');
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Get the average rating for a course.
     */
    public static function getAverageRatingForCourse(int $courseId): float
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT AVG(rating) FROM reviews WHERE course_id = :course_id');
        $stmt->execute(['course_id' => $courseId]);
        return round($stmt->fetchColumn() ?: 0, 1);
    }

    /**
     * Check if a specific user has already reviewed a course.
     */
    public static function hasUserReviewed(int $userId, int $courseId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND course_id = :course_id');
        $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Add a new review.
     */
    public static function create(int $userId, int $courseId, int $rating, ?string $comment): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO reviews (user_id, course_id, rating, comment) 
            VALUES (:user_id, :course_id, :rating, :comment)
        ');
        return $stmt->execute([
            'user_id' => $userId,
            'course_id' => $courseId,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }
}
