<?php

namespace App\Core;

class RateLimiter
{
    private string $storagePath;
    private string $logPath;
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 900) // 15 minutes
    {
        $this->storagePath = dirname(__FILE__, 3) . '/storage/ratelimit/';
        $this->logPath = dirname(__FILE__, 3) . '/storage/logs/ratelimit.log';
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
        if (!is_dir(dirname($this->logPath))) {
            mkdir(dirname($this->logPath), 0777, true);
        }
    }

    private function getIpAddress(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function getFilePath(string $key): string
    {
        return $this->storagePath . md5($key) . '.json';
    }

    private function log(string $key, string $action): void
    {
        $logEntry = json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'key' => $key,
            'ip' => $this->getIpAddress(),
            'action' => $action,
            'message' => 'Rate limit exceeded'
        ]) . PHP_EOL;

        file_put_contents($this->logPath, $logEntry, FILE_APPEND);
    }

    /**
     * Check if the key (e.g., IP address) has hit the rate limit.
     */
    public function isThrottled(string $key, string $action = 'general'): bool
    {
        $filePath = $this->getFilePath($key);

        if (!file_exists($filePath)) {
            return false;
        }

        $data = json_decode(file_get_contents($filePath), true);

        if (time() - $data['timestamp'] > $this->decaySeconds) {
            unlink($filePath);
            return false;
        }

        if ($data['attempts'] >= $this->maxAttempts) {
            // Only log the first time they are throttled to avoid log spam
            if (($data['attempts'] % $this->maxAttempts) === 0) {
                $this->log($key, $action);
            }
            return true;
        }

        return false;
    }

    /**
     * Record an attempt for a given key.
     */
    public function hit(string $key): void
    {
        $filePath = $this->getFilePath($key);
        $data = ['timestamp' => time(), 'attempts' => 1];

        if (file_exists($filePath)) {
            $existingData = json_decode(file_get_contents($filePath), true);
            
            if (time() - $existingData['timestamp'] > $this->decaySeconds) {
                $data['timestamp'] = time();
                $data['attempts'] = 1;
            } else {
                $data['timestamp'] = $existingData['timestamp'];
                $data['attempts'] = $existingData['attempts'] + 1;
            }
        }

        file_put_contents($filePath, json_encode($data));
    }

    /**
     * A convenient static method to check the current user's IP for a specific action.
     */
    public static function check(string $action = 'general'): bool
    {
        $limiter = new self();
        $key = $action . '_' . $limiter->getIpAddress();
        return $limiter->isThrottled($key, $action);
    }

    /**
     * A convenient static method to record an attempt for the current user's IP for a specific action.
     */
    public static function attempt(string $action = 'general'): void
    {
        $limiter = new self();
        $key = $action . '_' . $limiter->getIpAddress();
        $limiter->hit($key);
    }
}
