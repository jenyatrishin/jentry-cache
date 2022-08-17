<?php
declare(strict_types=1);

namespace Jentry\Tests;

use PHPUnit\Framework\TestCase;
use Jentry\Cache\Adapter\AdapterFactory;
use Jentry\Cache\Adapter\NullAdapter;
use Jentry\Cache\Exception\CacheInvalidArgumentException;

class AdapterFactoryTest extends TestCase
{
    public function testCreateObject(): void
    {
        $nullAdapter = AdapterFactory::create(NullAdapter::class);
        $this->assertSame(NullAdapter::class, get_class($nullAdapter));

        try {
            $nullAdapter = AdapterFactory::create(\StdClass::class);
        } catch (CacheInvalidArgumentException $e) {
            $this->assertEquals(
                'Incorrect adapter type',
                $e->getMessage()
            );
            return;
        }

        $this->fail('There should be incorrect adapter type error');
    }
}