<?php
declare(strict_types=1);

namespace Jentry\Cache\Adapter;

use Jentry\Cache\Traits\AdapterTrait;
use Jentry\Cache\Serialize\Marshaller;
use Jentry\Cache\Serialize\Serializer;
use Jentry\Cache\Exception\CacheInvalidArgumentException;

/**
 * Class NullAdapter
 *
 * Null type adapter
 */
final class NullAdapter implements Adapter
{
    use AdapterTrait;

    private int $ttl;
    private ?Marshaller $decoder;

    /**
     * NullAdapter constructor.
     * @param array $config
     * @param Marshaller|null $marshaller
     */
    public function __construct(array $config, Marshaller $marshaller = null)
    {
        $this->ttl = $config[self::TTL_KEY] ?? self::DEFAULT_TTL;
        $this->decoder = $marshaller;
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);

        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     *
     * @return void
     * @throws CacheInvalidArgumentException
     */
    public function save(string $key, mixed $value, \DateInterval|int|null $ttl = null): void
    {
        $this->validateKey($key);

        true;
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @throws CacheInvalidArgumentException
     */
    public function get(string $key): mixed
    {
        $this->validateKey($key);

        return null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isKeyExists(string $key): bool
    {
        return false;
    }
}