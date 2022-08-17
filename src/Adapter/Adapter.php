<?php
declare(strict_types=1);

namespace Jentry\Cache\Adapter;

interface Adapter
{
    /**
     * Adapter fallback constants
     */
    public const TTL_KEY = 'ttl';
    public const DEFAULT_TTL = 3600;

    /**
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     *
     * @return void
     */
    public function save(string $key, mixed $value, \DateInterval|int|null $ttl = null): void;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * @return bool
     */
    public function clear(): bool;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isKeyExists(string $key): bool;
}