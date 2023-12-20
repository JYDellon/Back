<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

class TokenBlacklistService
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function addToBlacklist(string $token): void
    {
        // Store the token in the cache with an appropriate TTL
        $item = $this->cache->getItem($token);
        $item->set(true)->expiresAfter(86400);
        $this->cache->saveDeferred($item);
        $this->cache->commit();
    }

    public function isTokenBlacklisted(string $token): bool
    {
        // Check if the token exists in the cache
        return $this->cache->getItem($token)->isHit();
    }
}
