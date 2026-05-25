<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Course
{
    public int $id;
    public string $name;
    public string $language;
    public string $difficulty_level;
    public string $description;
    public ?string $preview_image = null;
    public int $search_score = 0;

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

    public static function getEnrolledByUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT c.* 
            FROM courses c
            JOIN enrollments e ON c.id = e.course_id
            WHERE e.user_id = :user_id
            ORDER BY e.enrolled_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function searchAndFilterPaginated(
        ?string $search, 
        ?string $language, 
        ?string $difficulty, 
        ?string $sortBy, 
        ?string $enrolledFilter = null, 
        ?int $userId = null,
        int $limit = 6,
        int $offset = 0
    ): array {
        $db = Database::getInstance();
        
        $query = 'SELECT c.* FROM courses c ';
        $params = [];

        if ($enrolledFilter === 'yes' && $userId !== null) {
            $query .= ' JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = :user_id ';
            $params['user_id'] = $userId;
        } elseif ($enrolledFilter === 'no' && $userId !== null) {
            $query .= ' LEFT JOIN enrollments e ON c.id = e.course_id AND e.user_id = :user_id WHERE e.user_id IS NULL ';
            $params['user_id'] = $userId;
        } else {
            $query .= ' WHERE 1=1 ';
        }

        if (!empty($language)) {
            $query .= ' AND c.language = :language';
            $params['language'] = $language;
        }

        if (!empty($difficulty)) {
            $query .= ' AND c.difficulty_level = :difficulty';
            $params['difficulty'] = $difficulty;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $allCourses = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);

        if (!empty($search)) {
            $searchLower = strtolower(trim($search));
            $searchWords = array_filter(explode(' ', $searchLower));
            
            foreach ($allCourses as $course) {
                $nameLower = strtolower($course->name);
                $langLower = strtolower($course->language);
                
                $bestScore = 1000;

                if (str_contains($nameLower, $searchLower) || str_contains($langLower, $searchLower)) {
                    $bestScore = 0;
                } else {
                    $courseWords = array_merge(explode(' ', $nameLower), [$langLower]);
                    
                    foreach ($searchWords as $sWord) {
                        $sLen = strlen($sWord);
                        foreach ($courseWords as $cWord) {
                            $cLen = strlen($cWord);
                            if ($sWord === $cWord) {
                                $bestScore = min($bestScore, 0);
                            } else {
                                $fullDist = levenshtein($sWord, $cWord);
                                $bestScore = min($bestScore, $fullDist * 2); 
                                
                                if ($cLen >= $sLen && $sLen > 0) {
                                    $prefix = substr($cWord, 0, $sLen);
                                    $prefixDist = levenshtein($sWord, $prefix);
                                    $score = ($prefixDist * 3) + ($cLen - $sLen);
                                    $bestScore = min($bestScore, $score);
                                }
                            }
                        }
                    }
                }
                $course->search_score = $bestScore;
            }

            usort($allCourses, function ($a, $b) {
                return $a->search_score <=> $b->search_score;
            });
        } else {
            $allowedSorts = [
                'language_asc' => function($a, $b) { return $a->language <=> $b->language; },
                'language_desc' => function($a, $b) { return $b->language <=> $a->language; },
                'name_asc' => function($a, $b) { return $a->name <=> $b->name; },
                'difficulty_asc' => function($a, $b) {
                    $map = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3];
                    return $map[$a->difficulty_level] <=> $map[$b->difficulty_level];
                },
                'difficulty_desc' => function($a, $b) {
                    $map = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3];
                    return $map[$b->difficulty_level] <=> $map[$a->difficulty_level];
                }
            ];

            if (!empty($sortBy) && isset($allowedSorts[$sortBy])) {
                usort($allCourses, $allowedSorts[$sortBy]);
            } else {
                usort($allCourses, function($a, $b) { return $b->id <=> $a->id; });
            }
        }

        $totalFound = count($allCourses);
        $paginatedResults = array_slice($allCourses, $offset, $limit);

        return [
            'data' => $paginatedResults,
            'total' => $totalFound
        ];
    }

    public static function getDistinctLanguages(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT DISTINCT language FROM courses ORDER BY language');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function create(string $name, string $language, string $difficulty, string $description, ?string $previewImage): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO courses (name, language, difficulty_level, description, preview_image) VALUES (:name, :language, :difficulty, :description, :preview)');
        return $stmt->execute([
            'name' => $name,
            'language' => $language,
            'difficulty' => $difficulty,
            'description' => $description,
            'preview' => $previewImage
        ]);
    }

    public static function update(int $id, string $name, string $language, string $difficulty, string $description, ?string $previewImage): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE courses SET name = :name, language = :language, difficulty_level = :difficulty, description = :description, preview_image = :preview WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'language' => $language,
            'difficulty' => $difficulty,
            'description' => $description,
            'preview' => $previewImage
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM courses WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}