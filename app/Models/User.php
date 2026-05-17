<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public ?string $created_at = null; // Can be null before saving to DB

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        if ($stmt) {
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetchObject(self::class);
            return $user ?: null;
        }
        return null;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        if ($stmt) {
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetchObject(self::class);
            return $user ?: null;
        }
        return null;
    }

    public static function register(string $name, string $email, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        if ($stmt) {
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        }
        return false;
    }

    public static function login(string $email, string $password): ?self
    {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}