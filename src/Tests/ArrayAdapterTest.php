<?php
declare(strict_types=1);

namespace Jentry\Tests;

use PHPUnit\Framework\TestCase;
use Jentry\Cache\Adapter\ArrayAdapter;

class ArrayAdapterTest extends TestCase
{
    private ArrayAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new ArrayAdapter();
    }

    protected function tearDown(): void
    {
        $this->adapter->clear();
    }

    public function testGet(): void
    {
        $testValue = 1;
        $testKey = 'test_key';

        $this->adapter->save($testKey, $testValue);

        $this->assertIsInt($this->adapter->get($testKey));
        $this->assertNotNull($this->adapter->get($testKey));
        $this->assertNull($this->adapter->get('yuyuyu'));
    }

    public function testSave(): void
    {
        $testValue = 2;
        $testKey = 'test_key';

        $this->adapter->save($testKey, $testValue);

        $this->assertIsInt($this->adapter->get($testKey));
        $this->assertNotNull($this->adapter->get($testKey));

        $testValue = ['test'];
        $testKey = 'test_key_2';

        $this->adapter->save($testKey, $testValue);

        $this->assertIsArray($this->adapter->get($testKey));
        $this->assertNotNull($this->adapter->get($testKey));
    }

    public function testIsKeyExists(): void
    {
        $testValue = 3;
        $testKey = 'test_key';
        $this->adapter->save($testKey, $testValue);

        $this->assertTrue($this->adapter->isKeyExists($testKey));
        $this->assertFalse($this->adapter->isKeyExists('asdasdasd'));
    }

    public function testDelete(): void
    {
        $testValue = 4;
        $testKey = 'test_key';
        $this->adapter->save($testKey, $testValue);

        $this->adapter->delete($testKey);
        $this->assertFalse($this->adapter->isKeyExists($testKey));
        $this->assertNull($this->adapter->get($testKey));
    }

    public function testClear(): void
    {
        $testValue = 5;
        $testKey = 'test_key';
        $this->adapter->save($testKey, $testValue);

        $this->adapter->clear();
        $this->assertFalse($this->adapter->isKeyExists($testKey));
        $this->assertNull($this->adapter->get($testKey));
    }
}