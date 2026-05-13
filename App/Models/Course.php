<?php

namespace App\Models;

use App\Core\Database;

class Course
{
    public int $id;
    public string $name;
    public string $language;
    public string $description;

    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM Courses');
        
        // Fetch all rows as objects of this class
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }
}
