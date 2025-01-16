<?php

namespace App\Core;

use Redis;
use Exception;

trait RedisCache {
    private ?Redis $redis = null;

    /**
     * Connect to Redis
     */
    private function connectToRedis(): void {
        if ($this->redis === null) {
            $this->redis = new Redis();
            try {
                $this->redis->connect('redis', 6379); // Replace 'redis' with the hostname if needed
            } catch (Exception $e) {
                throw new Exception("Redis connection failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Get a cached value by key
     */
    public function get(string $key): mixed {
        $this->connectToRedis();
        $value = $this->redis->get($key);
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set a cached value
     */
    public function set(string $key, mixed $value, int $ttl = 3600): void {
        $this->connectToRedis();
        $this->redis->set($key, json_encode($value), $ttl);
    }

    /**
     * Clear a cached value
     */
    public function clear(string $key): void {
        $this->connectToRedis();
        $this->redis->del($key);
    }

    /**
     * Check if a key exists in Redis
     */
    public function has(string $key): bool {
        $this->connectToRedis();
        return $this->redis->exists($key) > 0;
    }
}
