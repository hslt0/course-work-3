<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Course
{
    public int $id;
    public string $name;
    public string $language;
    public string $description;
    public ?string $preview_image = null;

    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM courses');
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM courses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        $course = $stmt->fetchObject(self::class);
        return $course ?: null;
    }
}
