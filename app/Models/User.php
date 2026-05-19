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
    public string $role = 'student';
    public bool $is_banned = false;
    public ?string $created_at = null; 

    public static function getAllUsers(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, name, email, role, is_banned, created_at FROM users WHERE role != 'admin' ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetchObject(self::class);
        return $user ?: null;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetchObject(self::class);
        return $user ?: null;
    }

    public static function register(string $name, string $email, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public static function login(string $email, string $password): ?self
    {
        $user = self::findByEmail($email);
        // Do not allow banned users to login
        if ($user && !$user->is_banned && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public static function toggleBan(int $userId, bool $banStatus): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE users SET is_banned = :status WHERE id = :id AND role != "admin"');
        return $stmt->execute([
            'status' => $banStatus ? 1 : 0,
            'id' => $userId
        ]);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
