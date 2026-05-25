<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Question
{
    public int $id;
    public int $test_id;
    public string $question_text;

    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM questions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $question = $stmt->fetchObject(self::class);
        return $question ?: null;
    }

    public static function getForTest(int $testId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM questions WHERE test_id = :test_id ORDER BY id ');
        $stmt->execute(['test_id' => $testId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create(int $testId, string $questionText): int|false
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO questions (test_id, question_text) VALUES (:test_id, :question_text)');
        if ($stmt->execute(['test_id' => $testId, 'question_text' => $questionText])) {
            return (int)$db->query('SELECT LAST_INSERT_ID()')->fetchColumn();
        }
        return false;
    }

    public static function update(int $id, string $questionText): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE questions SET question_text = :question_text WHERE id = :id');
        return $stmt->execute(['id' => $id, 'question_text' => $questionText]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM questions WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}