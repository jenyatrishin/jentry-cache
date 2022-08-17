<?php
declare(strict_types=1);

namespace Jentry\Cache\Adapter;

use Jentry\Cache\Traits\AdapterTrait;
use Jentry\Cache\Serialize\Marshaller;
use Jentry\Cache\Serialize\Serializer;
use Jentry\Cache\Exception\CacheInvalidArgumentException;

/**
 * Class ArrayAdapter
 *
 * Cache array adapter
 */
final class ArrayAdapter implements Adapter
{
    use AdapterTrait;

    private array $data = [];
    private array $expiries = [];
    private int $ttl;
    private Marshaller $decoder;

    /**
     * ArrayAdapter constructor.
     * @param array $config
     * @param Marshaller|null $marshaller
     */
    public function __construct(array $config = [], Marshaller $marshaller = null)
    {
        $this->ttl = $config[self::TTL_KEY] ?? static::DEFAULT_TTL;
        $this->decoder = $marshaller ?? new Serializer();
    }

    /**
     * Retrieve cache value by key
     *
     * @param string $key
     *
     * @return mixed
     * @throws CacheInvalidArgumentException
     */
    public function get(string $key): mixed
    {
        $this->validateKey($key);
        $now = new \DateTimeImmutable('now');
        $end = $now->getTimestamp();
        if (empty($this->expiries[$key]) || $this->expiries[$key] < $end) {
            $this->delete($key);
            return null;
        }
        try {
            return $this->decoder->decode(($this->data[$key] ?? ''));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Store cache value
     *
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     *
     * @throws CacheInvalidArgumentException
     */
    public function save(string $key, mixed $value, \DateInterval|int|null $ttl = null): void
    {
        $this->validateKey($key);
        $expiresAt = $this->createExpiresString($ttl);
        $now = new \DateTimeImmutable('now');
        $end = $now->getTimestamp() + $expiresAt;
        $value = $this->decoder->encode($value);

        $this->data[$key] = $value;
        $this->expiries[$key] = $end ?: \PHP_INT_MAX;
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->data = [];
        $this->expiries = [];

        return true;
    }

    /**
     * Delete cache value by key
     *
     * @param string $key
     *
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);
        unset($this->data[$key]);
        unset($this->expiries[$key]);

        return true;
    }

    /**
     * Check if cache value exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function isKeyExists(string $key): bool
    {
        return isset($this->data[$key]);
    }
}