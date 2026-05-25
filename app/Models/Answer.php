<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Answer
{
    public int $id;
    public int $question_id;
    public string $answer_text;
    public bool $is_correct;

    public static function getForQuestion(int $questionId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM answers WHERE question_id = :question_id ORDER BY id ');
        $stmt->execute(['question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function create(int $questionId, string $answerText, int $isCorrect): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)');
        return $stmt->execute([
            'question_id' => $questionId,
            'answer_text' => $answerText,
            'is_correct' => $isCorrect
        ]);
    }

    public static function update(int $id, string $answerText, int $isCorrect): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE answers SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'answer_text' => $answerText,
            'is_correct' => $isCorrect
        ]);
    }

    public static function deleteByQuestionId(int $questionId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM answers WHERE question_id = :question_id');
        return $stmt->execute(['question_id' => $questionId]);
    }
}