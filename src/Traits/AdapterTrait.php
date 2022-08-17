<?php
declare(strict_types=1);

namespace Jentry\Cache\Traits;

use Jentry\Cache\Exception\CacheInvalidArgumentException;

/**
 * Trait AdapterTrait
 *
 * Common adapter methods
 */
trait AdapterTrait
{
    /**
     * @param \DateInterval|int|null $ttl
     *
     * @return int|null
     */
    public function createExpiresString(\DateInterval|int|null $ttl = null): int|null
    {
        if ($ttl === null) {
            return null;
        }

        if (is_int($ttl)) {
            return $ttl;
        }

            $now = new \DateTimeImmutable('now');
            $end = $now->add($ttl);

            return $end->getTimestamp() - $now->getTimestamp();
    }

    /**
     * @param string $key
     *
     * @throws CacheInvalidArgumentException
     */
    private function validateKey(string $key): void
    {
        if ($key === '') {
            throw new CacheInvalidArgumentException('Invalid key provided; cannot be empty');
        }

        $regex = sprintf('/[%s]/', preg_quote(':@{}()/\\', '/'));
        if (preg_match($regex, $key)) {
            throw new CacheInvalidArgumentException('Key contains incorrect symbols');
        }
    }
}