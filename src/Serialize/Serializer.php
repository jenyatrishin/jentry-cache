<?php
declare(strict_types=1);

namespace Jentry\Cache\Serialize;

use Jentry\Cache\Exception\CacheInvalidArgumentException;

/**
 * Class Serializer
 *
 * Serializer realisation based on serialize
 */
final class Serializer implements Marshaller
{
    /**
     * Unserialize data
     *
     * @param string $data
     *
     * @return mixed
     * @throws CacheInvalidArgumentException
     */
    public function decode(string $data): mixed
    {
        if ('' === $data) {
            throw new CacheInvalidArgumentException('Unable to unserialize value.');
        }

        return unserialize($data);
    }

    /**
     * Serialize data
     *
     * @param mixed $data
     *
     * @return string
     * @throws CacheInvalidArgumentException
     */
    public function encode(mixed $data): string
    {
        if (is_resource($data)) {
            throw new CacheInvalidArgumentException('Unable to serialize value.');
        }

        return serialize($data);
    }
}