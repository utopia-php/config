<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Adapters\JSON;
use Utopia\Config\Attributes\Key;
use Utopia\Config\Config;
use Utopia\Config\Loader;
use Utopia\Config\Sources\File;
use Utopia\Config\Sources\Variable;
use Utopia\Validator\Text;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DbConfig
{
    #[Key('api_key', new Text(1024, 0), required: true)]
    public string $apiKey;
}

class ConfigTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function testDaco(): void
    {
        $data = '{"api_key": "1234567890"}';
        $config = new Config(new Loader(new Variable($data), new JSON()));
        $config = $config->load(DbConfig::class);
        $this->assertSame('1234567890', $config->apiKey);

        $config = new Config(new Loader(new File(__DIR__.'/../resources/config.json'), new JSON()));
        $config = $config->load(DbConfig::class);
        $this->assertSame('456', $config->apiKey);
    }
}
