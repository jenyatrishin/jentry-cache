<?php
declare(strict_types=1);

namespace Jentry\Tests;

use Jentry\Cache\Adapter\ArrayAdapter;
use PHPUnit\Framework\TestCase;
use Jentry\Cache\Cache;

class CacheTest extends TestCase
{
    private Cache $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache(ArrayAdapter::class, []);
    }

    protected function tearDown(): void
    {
        $this->cache->clear();
    }

    public function testGet(): void
    {
        $testValue = 'cache test get value';
        $testKey = 'test_get_key';

        $this->cache->set($testKey, $testValue);

        $this->assertIsString($this->cache->get($testKey));
        $this->assertNotNull($this->cache->get($testKey));
        $this->cache->delete($testKey);
        $this->assertEquals($this->cache->get($testKey, 'default value'), 'default value');
    }

    public function testSet(): void
    {
        $testValue = 'cache test set value';
        $testKey = 'test_set_key';
        $this->cache->set($testKey, $testValue);

        $this->assertIsString($this->cache->get($testKey));

        $testArrayValue = ['cache test set value'];
        $testArrayKey = 'test_set_key';
        $this->cache->set($testArrayKey, $testArrayValue);
        $this->assertIsNotString($this->cache->get($testArrayKey));
        $this->assertIsArray($this->cache->get($testArrayKey));
    }

    public function testDelete(): void
    {
        $testValue = 'cache test set value';
        $testKey = 'test_set_key';
        $this->cache->set($testKey, $testValue);

        $this->cache->delete($testKey);

        $this->assertNull($this->cache->get($testKey));
        $this->assertFalse($this->cache->has($testKey));
    }

    public function testClear(): void
    {
        $testValue = 'cache test set value';
        $testKey = 'test_set_key';
        $this->cache->set($testKey, $testValue);

        $this->cache->clear();

        $this->assertNull($this->cache->get($testKey));
        $this->assertFalse($this->cache->has($testKey));
    }

    public function testHas(): void
    {
        $testValue = 'cache test has value';
        $testKey = 'test_has_key';
        $this->cache->set($testKey, $testValue);

        $this->assertTrue($this->cache->has($testKey));
        $this->assertFalse($this->cache->has('sdasdasd'));
        $this->cache->delete($testKey);
        $this->assertFalse($this->cache->has($testKey));
    }
}