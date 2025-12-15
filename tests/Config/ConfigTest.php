<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Parser\Dotenv;
use Utopia\Config\Parser\JSON;
use Utopia\Config\Parser\PHP;
use Utopia\Config\Parser\YAML;
use Utopia\Config\Attribute\Key;
use Utopia\Config\Config;
use Utopia\Config\Exception\Load;
use Utopia\Config\Parser;
use Utopia\Config\Source\File;
use Utopia\Validator\Text;

// Schemas used for configs in test scenarios
class TestConfig
{
    #[Key('phpKey', new Text(1024, 0), required: false)]
    public string $phpKey;

    #[Key('jsonKey', new Text(1024, 0), required: false)]
    public string $jsonKey;

    #[Key('yaml-key', new Text(1024, 0), required: false)]
    public string $yamlKey;

    #[Key('yml_key', new Text(1024, 0), required: false)]
    public string $ymlKey;

    #[Key('ENV_KEY', new Text(1024, 0), required: false)]
    public string $envKey;
}

// Tests themselves
class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    /**
     * @return array<mixed>
     */
    public static function provideAdapterScenarios(): array
    {
        return [
            [
                'adapter' => PHP::class,
                'extension' => 'php',
                'key' => 'phpKey'
            ],
            [
                'adapter' => JSON::class,
                'extension' => 'json',
                'key' => 'jsonKey'
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yaml',
                'key' => 'yamlKey'
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yml',
                'key' => 'ymlKey'
            ],
            [
                'adapter' => Dotenv::class,
                'extension' => 'env',
                'key' => 'envKey'
            ],
        ];
    }

    /**
     * @param  class-string  $adapter
     * @dataProvider provideAdapterScenarios
     */
    public function testAdapters(string $adapter, string $extension, string $key): void
    {
        $adapter = new $adapter();
        if (! ($adapter instanceof Parser)) {
            throw new \Exception('Test scenario includes invalid adapter.');
        }

        $config = Config::load(new File(__DIR__.'/../resources/config.'.$extension), $adapter, TestConfig::class);

        $this->assertSame('customValue', $config->$key);

        // Always keep last
        $this->expectException(Load::class);
        $config = Config::load(new File(__DIR__.'/../resources/non-existing.'.$extension), $adapter, TestConfig::class);
    }
}
