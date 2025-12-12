# Utopia Config

[![Build Status](https://travis-ci.org/utopia-php/ab.svg?branch=master)](https://travis-ci.com/utopia-php/config)
![Total Downloads](https://img.shields.io/packagist/dt/utopia-php/config.svg)
[![Discord](https://img.shields.io/discord/564160730845151244?label=discord)](https://appwrite.io/discord)

Utopia Config library is simple and lite library for managing application configuration. This library is aiming to be as simple and easy to learn and use. This library is maintained by the [Appwrite team](https://appwrite.io).

Although this library is part of the [Utopia Framework](https://github.com/utopia-php/framework) project it is dependency free and can be used as standalone with any other PHP project or framework.

## Getting Started

Install using composer:
```bash
composer require utopia-php/config
```

```php
<?php

require_once './vendor/autoload.php';

use Utopia\Config\Adapter\JSON;
use Utopia\Config\Attribute\Key;
use Utopia\Config\Config;
use Utopia\Config\Exception\Load;
use Utopia\Config\Exception\Parse;
use Utopia\Config\Loader;
use Utopia\Config\Source\File;
use Utopia\Validator\ArrayList;
use Utopia\Validator\Integer;
use Utopia\Validator\JSON as JSONValidator;
use Utopia\Validator\Nullable;
use Utopia\Validator\Text;

class DatabaseConfig
{
    #[Key('DB_HOST', new Text(length: 1024), required: true)]
    public string $host;

    #[Key('DB_PORT', new Integer(loose: true), required: false)]
    public ?int $port;

    #[Key('DB_USERNAME', new Text(length: 1024), required: true)]
    public string $username;

    #[Key('DB_PASSWORD', new Text(length: 1024), required: true)]
    public string $password;

    #[Key('DB_NAME', new Nullable(new Text(length: 1024)), required: true)]
    public ?string $name;

    /**
     * @var array<string, mixed> $config
     */
    #[Key('DB_CONFIG', new Nullable(new JSONValidator), required: true)]
    public ?array $config;

    /**
     * @var array<string> $whitelistIps
     */
    #[Key('DB_WHITELIST_IPS', new ArrayList(new Text(length: 100), length: 100), required: true)]
    public array $whitelistIps;
}

$loader = new Loader(new File(__DIR__.'/config.json'), new JSON);
$config = new Config($loader);

try {
    $dbConfig = $config->load(DatabaseConfig::class);
} catch (Load $err) {
    exit('Config could not be loaded from a file: ' . $err->getMessage());
} catch (Parse $err) {
    exit('Config could not be parsed as JSON: ' . $err->getMessage());
}

\var_dump($dbConfig);
// $dbConfig->host
// $dbConfig->port
// $dbConfig->username
// ...
```

For above example to work, make sure to setup `config.json` file too:

```json
{
  "DB_HOST": "127.0.0.1",
  "DB_PORT": 3306,
  "DB_USERNAME": "root",
  "DB_PASSWORD": "password",
  "DB_NAME": "utopia",
  "DB_CONFIG": {
    "timeout": 3000,
    "handshakeTimeout": 5000
  },
  "DB_WHITELIST_IPS": [
    "127.0.0.1",
    "172.17.0.0/16"
  ]
}
```

## System Requirements

Utopia Framework requires PHP 8.0 or later. We recommend using the latest PHP version whenever possible.

When using YAML adapter, or running tests with it, you need to install the YAML extension for PHP.

## Copyright and license

The MIT License (MIT) [http://www.opensource.org/licenses/mit-license.php](http://www.opensource.org/licenses/mit-license.php)
