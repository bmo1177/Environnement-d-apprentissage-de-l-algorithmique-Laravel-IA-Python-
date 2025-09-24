<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Closure;

/**
 * Cache Service
 * 
 * Provides optimized caching strategies for improved application performance
 */
class CacheService
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    protected $defaultTtl = 3600;

    /**
     * Cache prefix for all keys
     */
    protected $prefix = 'learner_env_';

    /**
     * Get or set cache with automatic key generation
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @param Closure $callback Function to generate data if not cached
     * @param int|null $ttl Time to live in seconds
     * @return mixed Cached data
     */
    public function remember(string $group, $identifier, Closure $callback, ?int $ttl = null)
    {
        $key = $this->generateCacheKey($group, $identifier);
        $ttl = $ttl ?? $this->defaultTtl;
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Store data in cache
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @param mixed $data Data to cache
     * @param int|null $ttl Time to live in seconds
     * @return bool Success status
     */
    public function put(string $group, $identifier, $data, ?int $ttl = null)
    {
        $key = $this->generateCacheKey($group, $identifier);
        $ttl = $ttl ?? $this->defaultTtl;
        
        return Cache::put($key, $data, $ttl);
    }

    /**
     * Get data from cache
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @param mixed $default Default value if not found
     * @return mixed Cached data or default
     */
    public function get(string $group, $identifier, $default = null)
    {
        $key = $this->generateCacheKey($group, $identifier);
        return Cache::get($key, $default);
    }

    /**
     * Check if key exists in cache
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @return bool Whether key exists
     */
    public function has(string $group, $identifier)
    {
        $key = $this->generateCacheKey($group, $identifier);
        return Cache::has($key);
    }

    /**
     * Remove data from cache
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @return bool Success status
     */
    public function forget(string $group, $identifier)
    {
        $key = $this->generateCacheKey($group, $identifier);
        return Cache::forget($key);
    }

    /**
     * Clear all cache for a group
     *
     * @param string $group Cache group name
     * @return bool Success status
     */
    public function clearGroup(string $group)
    {
        $pattern = $this->prefix . $group . '_*';
        $keys = $this->getKeysMatchingPattern($pattern);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        return true;
    }

    /**
     * Generate a cache key from group and identifier
     *
     * @param string $group Cache group name
     * @param string|array $identifier Unique identifier(s)
     * @return string Cache key
     */
    protected function generateCacheKey(string $group, $identifier)
    {
        if (is_array($identifier)) {
            $identifier = implode('_', $identifier);
        }
        
        return $this->prefix . $group . '_' . md5($identifier);
    }

    /**
     * Get all cache keys matching a pattern
     *
     * @param string $pattern Pattern to match
     * @return array Matching keys
     */
    protected function getKeysMatchingPattern(string $pattern)
    {
        // This is a simplified implementation that works with file cache
        // For Redis, you would use the KEYS command
        $keys = [];
        
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys($pattern);
            } catch (\Exception $e) {
                Log::error('Redis cache pattern matching failed', [
                    'pattern' => $pattern,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $keys;
    }
}