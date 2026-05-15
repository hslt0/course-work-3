<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Question
{
    public int $id;
    public int $test_id;
    public string $question_text;

    /**
     * Fetches all questions for a given test ID.
     * @return self[]
     */
    public static function getForTest(int $testId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM questions WHERE test_id = :test_id ORDER BY id ASC');
        $stmt->execute(['test_id' => $testId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
