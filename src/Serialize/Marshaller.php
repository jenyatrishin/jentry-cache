<?php
declare(strict_types=1);

namespace Jentry\Cache\Serialize;

/**
 * Interface Marshaller
 *
 * Serializer interface
 */
interface Marshaller
{
    /**
     * @param mixed $data
     *
     * @return string
     */
    public function encode(mixed $data): string;

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function decode(string $data): mixed;
}