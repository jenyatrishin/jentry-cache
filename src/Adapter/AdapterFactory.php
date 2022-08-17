<?php
declare(strict_types=1);

namespace Jentry\Cache\Adapter;

use Jentry\Cache\Exception\CacheInvalidArgumentException;

/**
 * Class AdapterFactory
 *
 * Cache adapter factory
 */
final class AdapterFactory
{
    /**
     * Instantiate adapter object by class name
     *
     * @param string $type
     * @param array $config
     *
     * @psalm-suppress InvalidStringClass
     * @return Adapter
     * @throws CacheInvalidArgumentException
     */
    public static function create(string $type, array $config = []): Adapter
    {
        $instance = new $type($config);
        if (!($instance instanceof Adapter)) {
            throw new CacheInvalidArgumentException('Incorrect adapter type');
        }

        return $instance;
    }
}