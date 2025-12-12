<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Adapter;
use Utopia\Config\Adapter\Dotenv;
use Utopia\Config\Adapter\JSON;
use Utopia\Config\Adapter\PHP;
use Utopia\Config\Adapter\YAML;
use Utopia\Config\Attribute\Key;
use Utopia\Config\Config;
use Utopia\Config\Exception\Load;
use Utopia\Config\Loader;
use Utopia\Config\Source\File;
use Utopia\Validator\ArrayList;
use Utopia\Validator\JSON as ValidatorJSON;
use Utopia\Validator\Nullable;
use Utopia\Validator\Text;

// Initial setup
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Schemas used for configs in test scenarios
class TestConfig
{
    #[Key('key', new Text(1024, 0), required: true)]
    public string $key;

    #[Key('keyWithComment', new Nullable(new Text(1024, 0)), required: false)]
    public ?string $keyWithComment;

    /**
     * @var array<string> $array
     */
    #[Key('array', new Nullable(new ArrayList(new Text(1024, 0), 100)), required: false)]
    public ?array $array;

    /**
     * @var array<string, string> $nested
     */
    #[Key('nested', new Nullable(new ValidatorJSON()), required: false)]
    public ?array $nested;
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
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => JSON::class,
                'extension' => 'json',
                'comments' => false,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yaml',
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => YAML::class,
                'extension' => 'yml',
                'comments' => true,
                'arrays' => true,
                'objects' => true,
            ],
            [
                'adapter' => Dotenv::class,
                'extension' => 'env',
                'comments' => true,
                'arrays' => false,
                'objects' => false,
            ],
        ];
    }

    /**
     * @param  class-string  $adapter
     * @dataProvider provideAdapterScenarios
     */
    public function testAdapters(string $adapter, string $extension, bool $comments = true, bool $arrays = true, bool $objects = true): void
    {
        $adapter = new $adapter();
        if (! ($adapter instanceof Adapter)) {
            throw new \Exception('Test scenario includes invalid adapter.');
        }

        $config = new Config(new Loader(new File(__DIR__.'/../resources/config.'.$extension), $adapter));
        $testConfig = $config->load(TestConfig::class);

        $this->assertSame('keyValue', $testConfig->key);

        if ($comments) {
            $this->assertSame('keyWithCommentValue', $testConfig->keyWithComment);
        }

        if ($arrays) {
            $this->assertNotNull($testConfig->array);
            $this->assertIsArray($testConfig->array);
            $this->assertCount(2, $testConfig->array);
            $this->assertSame('arrayValue1', $testConfig->array[0]);
            $this->assertSame('arrayValue2', $testConfig->array[1]);
        }

        if ($objects) {
            $this->assertNotNull($testConfig->nested);
            $this->assertArrayHasKey('key', $testConfig->nested);
            $this->assertSame('nestedKeyValue', $testConfig->nested['key']);
        }

        // Always keep last
        $this->expectException(Load::class);
        $config = new Config(new Loader(new File(__DIR__.'/../resources/non-existing.'.$extension), $adapter));
        $testConfig = $config->load(TestConfig::class);
    }
}
