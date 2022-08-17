<?php
declare(strict_types=1);

namespace Jentry\Cache;

use Psr\SimpleCache\CacheInterface;
use Jentry\Cache\Adapter\Adapter;
use Jentry\Cache\Adapter\AdapterFactory;

/**
 * Class Cache
 *
 * PSR-16 Cache implementation
 */
final class Cache implements CacheInterface
{
    /**
     * @var Adapter
     */
    private Adapter $adapter;

    /**
     * Cache constructor.
     * @param string $type
     * @param array $config
     * @throws Exception\CacheInvalidArgumentException
     */
    public function __construct(string $type, array $config)
    {
        $this->adapter = AdapterFactory::create($type, $config);
    }

    /**
     * Retrieve cache data
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->adapter->get($key) ?? $default;
    }

    /**
     * Delete data by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->adapter->delete($key);
    }

    /**
     * Clear cache storage
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->adapter->clear();
    }

    /**
     * Delete by multiple keys
     *
     * @param iterable $keys
     *
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $res = [];
        foreach ($keys as $key) {
            $res[] = $this->delete($key);
        }

        return !in_array(false, $res);
    }

    /**
     * Retrieve by multiple keys
     *
     * @param iterable $keys
     * @param mixed|null $default
     *
     * @return \Generator<int, mixed, mixed, void>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key);
        }
    }

    /**
     * Check if there's cache value by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->adapter->isKeyExists($key);
    }

    /**
     * Store cache data
     *
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     *
     * @return bool
     */
    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        $this->adapter->save($key, $value, $ttl);

        return true;
    }

    /**
     * Store cache data by multiple values
     *
     * @param iterable $values
     * @param \DateInterval|int|null $ttl
     *
     * @return bool
     */
    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }
}