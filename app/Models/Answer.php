<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Answer
{
    public int $id;
    public int $question_id;
    public string $answer_text;
    public bool $is_correct; // Use 1 for true, 0 for false in DB

    /**
     * Fetches all possible answers for a given question ID.
     * @return self[]
     */
    public static function getForQuestion(int $questionId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM answers WHERE question_id = :question_id ORDER BY id ASC');
        $stmt->execute(['question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
