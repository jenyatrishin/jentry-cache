<?php
declare(strict_types=1);

namespace Jentry\Tests;

use PHPUnit\Framework\TestCase;
use Jentry\Cache\Adapter\Filesystem;
use Jentry\Cache\Exception\CacheInvalidArgumentException;
use Jentry\Cache\Exception\FilesystemException;

class FilesystemAdapterTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem([
            'namespace' => __DIR__.'/testcache',
            'prefix' => 'test_cache'
        ]);
    }

    protected function tearDown(): void
    {
        $this->filesystem->clear();
        rmdir(__DIR__.'/testcache');
    }


    public function testSave(): void
    {
        $value = 'test value';
        $valueKey = 'testkey';

        $this->filesystem->save($valueKey, $value, 6400);
        $this->assertFileExists(__DIR__.'/testcache/test_cache---' . $valueKey);
        $this->assertFileIsReadable(__DIR__.'/testcache/test_cache---' . $valueKey);
        $this->assertFileIsWritable(__DIR__.'/testcache/test_cache---' . $valueKey);

        $this->assertEquals($value, $this->filesystem->get($valueKey));
    }

    public function testGet(): void
    {
        $value = 'test get value';
        $valueKey = 'testgetkey';

        $this->filesystem->save($valueKey, $value, 6400);
        $this->assertEquals($value, $this->filesystem->get($valueKey));

        $arrayValue = ['test' => 'test get value'];
        $arrayValueKey = 'testgetkey';
        $this->filesystem->save($arrayValueKey, $arrayValue, 6400);
        $this->assertIsArray($this->filesystem->get($arrayValueKey));
        $this->assertArrayHasKey('test', $this->filesystem->get($arrayValueKey));

        $this->assertNull($this->filesystem->get('ytyutuytutuyjhj'));
    }

    public function testIsKeyExists(): void
    {
        $value = 'test exists value';
        $valueKey = 'testexistskey';

        $this->filesystem->save($valueKey, $value, 6400);
        $this->assertTrue($this->filesystem->isKeyExists($valueKey));

        $this->assertFalse($this->filesystem->isKeyExists('khjkhkjhkjh'));
    }

    public function testClear(): void
    {
        $value = 'test clear value';
        $valueKey = 'testclearskey';

        $this->filesystem->save($valueKey, $value, 6400);
        $this->filesystem->clear();

        $this->assertFileDoesNotExist(__DIR__.'/testcache/test_cache---' . $valueKey);
        $this->assertNull($this->filesystem->get($valueKey));
        $this->assertFalse($this->filesystem->isKeyExists($valueKey));
    }

    public function testDelete(): void
    {
        $value = 'test delete value';
        $valueKey = 'testdeleteskey';

        $this->filesystem->save($valueKey, $value, 6400);
        $this->filesystem->delete($valueKey);

        $this->assertFileDoesNotExist(__DIR__.'/testcache/test_cache---' . $valueKey);
        $this->assertNull($this->filesystem->get($valueKey));
        $this->assertFalse($this->filesystem->isKeyExists($valueKey));
    }

    /** TODO: move this methods to trait test file */
    public function testSaveKeySymbolsException(): void
    {
        try {
            $this->filesystem->save('hjhj@{', 'test');
        } catch (CacheInvalidArgumentException $e) {
            $this->assertEquals(
                'Key contains incorrect symbols',
                $e->getMessage()
            );
            return;
        }

        $this->fail('There should be incorrect symbols error');
    }

    public function testSaveKeyBlankException(): void
    {
        try {
            $this->filesystem->save('', 'test');
        } catch (CacheInvalidArgumentException $e) {
            $this->assertEquals(
                'Invalid key provided; cannot be empty',
                $e->getMessage()
            );
            return;
        }

        $this->fail('There should be blank key error');
    }
}