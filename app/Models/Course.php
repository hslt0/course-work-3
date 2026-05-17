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

    /**
     * Gets all courses a specific user is enrolled in.
     */
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

    /**
     * Search and filter courses.
     * Uses PHP Levenshtein distance to score and sort all results based on closeness to the search query.
     */
    public static function searchAndFilter(?string $search, ?string $language, ?string $difficulty, ?string $sortBy, ?string $enrolledFilter = null, ?int $userId = null): array
    {
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

        // Apply dropdown filters directly in SQL
        if (!empty($language)) {
            $query .= ' AND c.language = :language';
            $params['language'] = $language;
        }

        if (!empty($difficulty)) {
            $query .= ' AND c.difficulty_level = :difficulty';
            $params['difficulty'] = $difficulty;
        }

        // Validate sort by to prevent SQL injection
        $allowedSorts = [
            'language_asc' => 'c.language ASC',
            'language_desc' => 'c.language DESC',
            'difficulty_asc' => 'FIELD(c.difficulty_level, "Beginner", "Intermediate", "Advanced") ASC',
            'difficulty_desc' => 'FIELD(c.difficulty_level, "Beginner", "Intermediate", "Advanced") DESC',
            'name_asc' => 'c.name ASC',
        ];

        // Only apply SQL sorting if they selected a specific dropdown sort
        if (!empty($sortBy) && array_key_exists($sortBy, $allowedSorts)) {
            $query .= ' ORDER BY ' . $allowedSorts[$sortBy];
        } else {
            $query .= ' ORDER BY c.id DESC'; // Default sorting
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $allCourses = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);

        // If no search term, return the SQL-filtered list
        if (empty($search)) {
            return $allCourses;
        }

        // ---------------------------------------------------------
        // Perform PHP-based fuzzy search scoring
        // ---------------------------------------------------------
        $searchLower = strtolower(trim($search));
        $searchWords = array_filter(explode(' ', $searchLower));

        foreach ($allCourses as $course) {
            $nameLower = strtolower($course->name);
            $langLower = strtolower($course->language);
            
            $bestScore = 1000; // High number represents a bad match (distance)

            // 1. Exact or partial match gets a perfect score (0)
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
                            // Calculate full word distance
                            $fullDist = levenshtein($sWord, $cWord);
                            $bestScore = min($bestScore, $fullDist * 2); // Penalty for full-word typos
                            
                            // Calculate prefix distance for partial typing (e.g. "Ing" matching "English")
                            if ($cLen >= $sLen && $sLen > 0) {
                                $prefix = substr($cWord, 0, $sLen);
                                $prefixDist = levenshtein($sWord, $prefix);
                                
                                // Score = (typos in prefix * 3) + penalty for the remaining untyped letters
                                $score = ($prefixDist * 3) + ($cLen - $sLen);
                                $bestScore = min($bestScore, $score);
                            }
                        }
                    }
                }
            }
            
            $course->search_score = $bestScore;
        }

        // Sort all courses by their search_score (lowest/closest first)
        usort($allCourses, function ($a, $b) {
            return $a->search_score <=> $b->search_score;
        });

        // We return ALL courses. The closest matches are naturally pushed to the top!
        return $allCourses;
    }

    /**
     * Get distinct languages for the filter dropdown.
     */
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
