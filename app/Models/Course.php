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
     * Search and filter courses.
     * Uses PHP Levenshtein distance to score and sort all results based on closeness to the search query.
     */
    public static function searchAndFilter(?string $search, ?string $language, ?string $difficulty, ?string $sortBy): array
    {
        $db = Database::getInstance();
        
        $query = 'SELECT * FROM courses WHERE 1=1';
        $params = [];

        // Apply dropdown filters directly in SQL
        if (!empty($language)) {
            $query .= ' AND language = :language';
            $params['language'] = $language;
        }

        if (!empty($difficulty)) {
            $query .= ' AND difficulty_level = :difficulty';
            $params['difficulty'] = $difficulty;
        }

        // Validate sort by to prevent SQL injection
        $allowedSorts = [
            'language_asc' => 'language ASC',
            'language_desc' => 'language DESC',
            'difficulty_asc' => 'FIELD(difficulty_level, "Beginner", "Intermediate", "Advanced") ASC',
            'difficulty_desc' => 'FIELD(difficulty_level, "Beginner", "Intermediate", "Advanced") DESC',
            'name_asc' => 'name ASC',
        ];

        // Only apply SQL sorting if they selected a specific dropdown sort
        if (!empty($sortBy) && array_key_exists($sortBy, $allowedSorts)) {
            $query .= ' ORDER BY ' . $allowedSorts[$sortBy];
        } else {
            $query .= ' ORDER BY id DESC'; // Default sorting
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
}
