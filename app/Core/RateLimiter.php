<?php

namespace App\Core;

class RateLimiter
{
    private string $storagePath;
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 900) // 15 minutes
    {
        $this->storagePath = dirname(__FILE__, 3) . '/storage/ratelimit/';
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
        // No log path or log directory creation needed anymore
    }

    private function getIpAddress(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function getFilePath(string $key): string
    {
        return $this->storagePath . md5($key) . '.json';
    }

    /**
     * Check if the key (e.g., IP address) has hit the rate limit.
     */
    public function isThrottled(string $key): bool
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

        return $data['attempts'] >= $this->maxAttempts;
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
     * @param string $action A unique identifier for the action being rate-limited.
     * @param int $maxAttempts The maximum number of attempts allowed.
     * @param int $decaySeconds The time window in seconds for the rate limit.
     */
    public static function check(string $action = 'general', int $maxAttempts = 5, int $decaySeconds = 900): bool
    {
        $limiter = new self($maxAttempts, $decaySeconds);
        $key = $action . '_' . $limiter->getIpAddress();
        return $limiter->isThrottled($key);
    }

    /**
     * A convenient static method to record an attempt for the current user's IP for a specific action.
     * @param string $action A unique identifier for the action being rate-limited.
     * @param int $maxAttempts The maximum number of attempts allowed (used for initializing if needed).
     * @param int $decaySeconds The time window in seconds for the rate limit (used for initializing if needed).
     */
    public static function attempt(string $action = 'general', int $maxAttempts = 5, int $decaySeconds = 900): void
    {
        $limiter = new self($maxAttempts, $decaySeconds);
        $key = $action . '_' . $limiter->getIpAddress();
        $limiter->hit($key);
    }
}
