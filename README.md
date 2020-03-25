# Utopia Config

[![Build Status](https://travis-ci.org/utopia-php/config.svg?branch=master)](https://travis-ci.org/utopia-php/ab)
![Total Downloads](https://img.shields.io/packagist/dt/utopia-php/config.svg)
[![Chat With Us](https://img.shields.io/gitter/room/utopia-php/community.svg)](https://gitter.im/utopia-php/community?utm_source=share-link&utm_medium=link&utm_campaign=share-link)

Utopia Config library is simple and lite library for managing application configuration. This library is aiming to be as simple and easy to learn and use.

Although this library is part of the [Utopia Framework](https://github.com/utopia-php/framework) project it is dependency free and can be used as standalone with any other PHP project or framework.

## Getting Started

Install using composer:
```bash
composer require utopia-php/config
```

```php
<?php

require_once '../vendor/autoload.php';

use Utopia\Config\Config;

// Basic params
Config::setParam('key', 'value');
Config::getParam('key'); // Value
Config::getParam('keyx', 'default'); // default

// Nested params
Config::setParam('key3', ['key4' => 'value4']);
Config::getParam('key3'); // ['key4' => 'value4']
Config::getParam('key3.key4'); // value4

// Load config file (plain array)
Config::load('key5', __DIR__.'/demo.php');
Config::getParam('key5.key1', 'default'); // value1

```

## System Requirements

Utopia Framework requires PHP 7.1 or later. We recommend using the latest PHP version whenever possible.

## Authors

**Eldad Fux**

+ [https://twitter.com/eldadfux](https://twitter.com/eldadfux)
+ [https://github.com/eldadfux](https://github.com/eldadfux)

## Copyright and license

The MIT License (MIT) [http://www.opensource.org/licenses/mit-license.php](http://www.opensource.org/licenses/mit-license.php)