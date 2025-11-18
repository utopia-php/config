<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Adapter;
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
        $this->assertEquals('value', Config::getParam('key'));
        $this->assertEquals('default', Config::getParam('keyx', 'default'));

        Config::setParam('key', 'value2');
        $this->assertEquals('value2', Config::getParam('key'));

        Config::setParam('key2', 'value2');
        $this->assertEquals('value2', Config::getParam('key2'));

        Config::setParam('key3', ['key4' => 'value4']);
        $this->assertEquals(['key4' => 'value4'], Config::getParam('key3'));
        $this->assertEquals('value4', Config::getParam('key3.key4'));
        $this->assertEquals('default', Config::getParam('key3.keyx', 'default'));
        $this->assertEquals('default', Config::getParam('key3.key4.x', 'default'));
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
            ],
            [
                'adapter' => JSON::class,
                'extension' => 'json',
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yaml',
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yml',
            ],
        ];
    }

    /**
     * @param  class-string  $adapter
     * @param  string  $extension
     *
     * @dataProvider proviteAdapterScenarios
     */
    public function testAdapters(string $adapter, string $extension): void
    {
        $key = $extension;

        $adpater = new $adapter();
        if (! ($adpater instanceof Adapter)) {
            throw new \Exception('Test scenario includes invalid adapter.');
        }

        Config::load($key, __DIR__.'/../resources/config.'.$extension, $adpater);

        $this->assertEquals('keyValue', Config::getParam($key.'.key'));
        $this->assertEquals('nestedKeyValue', Config::getParam($key.'.nested.key'));
        $this->assertIsArray(Config::getParam($key.'.array'));
        $this->assertCount(2, Config::getParam($key.'.array'));
        $this->assertEquals('arrayValue1', Config::getParam($key.'.array')[0]);
        $this->assertEquals('arrayValue2', Config::getParam($key.'.array')[1]);
        $this->assertEquals('mirroredValue', Config::getParam($key.'.mirrored'));
        $this->assertEquals('defaultValue', Config::getParam($key.'.non-existing-key', 'defaultValue'));

        // TODO: In future, improve test for more structures (empty object, empty array, more nested objects, nested array in object, nested object in array, numbers, floats, booleans, ..)

        // Always keep last
        $this->expectException(Load::class);
        Config::load($key, __DIR__.'/non-existing.'.$extension, $adpater);
    }
}
