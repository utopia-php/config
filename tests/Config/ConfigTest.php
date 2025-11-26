<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Adapter;
use Utopia\Config\Adapters\Dotenv;
use Utopia\Config\Adapters\JSON;
use Utopia\Config\Adapters\PHP;
use Utopia\Config\Adapters\YAML;
use Utopia\Config\Config;
use Utopia\Config\Exceptions\Load;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ConfigTest extends TestCase
{
    public function setUp(): void
    {
        // Used in some adapter tests (like PHP) to ensure values can be mirrored
        Config::setParam('mirrored', 'mirroredValue');
    }

    public function tearDown(): void
    {
    }

    public function testSetParam(): void
    {
        Config::setParam('key', 'value');
        $this->assertSame('value', Config::getParam('key'));
        $this->assertSame('default', Config::getParam('keyx', 'default'));

        Config::setParam('key', 'value2');
        $this->assertSame('value2', Config::getParam('key'));

        Config::setParam('key2', 'value2');
        $this->assertSame('value2', Config::getParam('key2'));

        Config::setParam('key3', ['key4' => 'value4']);
        $this->assertSame(['key4' => 'value4'], Config::getParam('key3'));
        $this->assertSame('value4', Config::getParam('key3.key4'));
        $this->assertSame('default', Config::getParam('key3.keyx', 'default'));
        $this->assertSame('default', Config::getParam('key3.key4.x', 'default'));
    }

    /**
     * @return array<mixed>
     */
    public function proviteAdapterScenarios(): array
    {
        return [
            [
                'adapter' => PHP::class,
                'extension' => 'php',
                'mirroring' => true,
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => JSON::class,
                'extension' => 'json',
                'mirroring' => false,
                'comments' => false,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yaml',
                'mirroring' => false,
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yml',
                'mirroring' => false,
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => Dotenv::class,
                'extension' => 'env',
                'mirroring' => false,
                'comments' => true,
                'arrays' => false,
                'objects' => false,
            ],
        ];
    }

    /**
     * @param  class-string  $adapter
     * @param  string  $extension
     * @param  bool  $mirroring
     * @param  bool  $comments
     * @param  bool  $arrays
     * @param  bool  $objects
     *
     * @dataProvider proviteAdapterScenarios
     */
    public function testAdapters(string $adapter, string $extension, bool $mirroring = true, bool $comments = true, bool $arrays = true, bool $objects = true): void
    {
        $key = $extension;

        $adpater = new $adapter();
        if (! ($adpater instanceof Adapter)) {
            throw new \Exception('Test scenario includes invalid adapter.');
        }

        Config::load($key, __DIR__.'/../resources/config.'.$extension, $adpater);

        $this->assertSame('keyValue', Config::getParam($key.'.key'));

        if ($comments) {
            $this->assertSame('keyWithCommentValue', Config::getParam($key.'.keyWithComment'));
        }

        if ($mirroring) {
            $this->assertSame('mirroredValue', Config::getParam($key.'.mirrored'));
        }

        if ($arrays) {
            $this->assertIsArray(Config::getParam($key.'.array'));
            $this->assertCount(2, Config::getParam($key.'.array'));
            $this->assertSame('arrayValue1', Config::getParam($key.'.array')[0]);
            $this->assertSame('arrayValue2', Config::getParam($key.'.array')[1]);
        }

        if ($objects) {
            $this->assertSame('nestedKeyValue', Config::getParam($key.'.nested.key'));
        }

        $this->assertSame('defaultValue', Config::getParam($key.'.non-existing-key', 'defaultValue'));

        // TODO: In future, improve test for more structures (empty object, empty array, more nested objects, nested array in object, nested object in array, numbers, floats, booleans, ..)

        // Always keep last
        $this->expectException(Load::class);
        Config::load($key, __DIR__.'/non-existing.'.$extension, $adpater);
    }
}
