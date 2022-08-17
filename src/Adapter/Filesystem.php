<?php
declare(strict_types=1);

namespace Jentry\Cache\Adapter;

use Jentry\Cache\Serialize\Marshaller;
use Jentry\Cache\Serialize\Serializer;
use Jentry\Cache\Exception\CacheInvalidArgumentException;
use Jentry\Cache\Exception\FilesystemException;
use Jentry\Cache\Traits\AdapterTrait;

/**
 * Class Filesystem
 *
 * Cache filesystem adapter
 */
final class Filesystem implements Adapter
{
    use AdapterTrait;

    private int $ttl;
    private string $namespace;
    private string $prefix = 'jentry_cache';
    private Marshaller $decoder;

    /**
     * Filesystem constructor.
     * @param array $config
     * @param Marshaller|null $marshaller
     * @throws CacheInvalidArgumentException
     */
    public function __construct(array $config, Marshaller $marshaller = null)
    {
        $this->ttl = $config[self::TTL_KEY] ?? static::DEFAULT_TTL;
        $this->namespace = $config['namespace'];
        if (!empty($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
        $this->decoder = $marshaller ?? new Serializer();
        $this->createCacheFolder();
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
        $fileName = $this->prepareFileName($key);
        $data = null;
        if (file_exists($fileName)) {
            if (!$this->isExpired($fileName)) {
                $file = fopen($fileName, "r+");
                try {
                    $data = fread($file, filesize($fileName));
                } catch (\Exception $e) {
                    $data = null;
                } finally {
                    fclose($file);
                }
            } else {
                $this->removeFile($fileName);
            }
        }

        return $data ? $this->decoder->decode($data) : null;
    }

    /**
     * Check if cache value exists
     *
     * @param string $key
     *
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    public function isKeyExists(string $key): bool
    {
        $value = $this->get($key);

        return $value !== null;
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
        $fileName = $this->prepareFileName($key);
        $this->removeFile($fileName);

        $expiresAt = $this->createExpiresString($ttl);
        $value = $this->decoder->encode($value);

        $file = fopen($fileName, 'w+');
        fwrite($file, $value);
        fclose($file);
        touch($fileName, (time() + $expiresAt));
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        foreach (new \DirectoryIterator($this->namespace) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->isWritable()) {
                $this->removeFile($fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->getFilename());
            }
        }

        return true;
    }

    /**
     * Delete cache file by key
     *
     * @param string $key
     *
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);
        $fileName = $this->prepareFileName($key);

        return $this->removeFile($fileName);
    }

    /**
     * Prepare file name
     *
     * @param string $id
     *
     * @return string
     */
    private function prepareFileName(string $id): string
    {
        return $this->namespace . DIRECTORY_SEPARATOR . $this->prefix . '---' . $id;
    }

    /**
     * Check if file expired
     *
     * @param string $fileName
     *
     * @return bool
     */
    private function isExpired(string $fileName): bool
    {
        return @filemtime($fileName) <= time();
    }

    /**
     * Remove cache file
     *
     * @param string $fileName
     *
     * @return bool
     */
    private function removeFile(string $fileName): bool
    {
        if (file_exists($fileName)) {
            return unlink($fileName);
        }

        return false;
    }

    /**
     * Create cache folder
     *
     * @throws CacheInvalidArgumentException
     */
    private function createCacheFolder(): void
    {
        if (!is_dir($this->namespace)) {
            mkdir($this->namespace, 0777, true);
        }
        if (!is_writeable($this->namespace)) {
            throw new CacheInvalidArgumentException('Directory cache is not writeable');
        }
    }
}