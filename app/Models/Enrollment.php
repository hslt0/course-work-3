<?php

namespace App\Models;

use App\Core\Database;

class Enrollment
{
    public int $id;
    public int $user_id;
    public int $course_id;
    public string $enrolled_at;

    public static function isEnrolled(int $userId, int $courseId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM enrollments WHERE user_id = :user_id AND course_id = :course_id');
        $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function enroll(int $userId, int $courseId): bool
    {
        if (self::isEnrolled($userId, $courseId)) {
            return true;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (:user_id, :course_id)');
        return $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
    }

    public static function unenroll(int $userId, int $courseId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM enrollments WHERE user_id = :user_id AND course_id = :course_id');
        return $stmt->execute(['user_id' => $userId, 'course_id' => $courseId]);
    }
}