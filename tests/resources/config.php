<?php

use Utopia\Config\Config;

return [
    'key' => 'keyValue',
    'nested' => ['key' => 'nestedKeyValue'],
    'array' => ['arrayValue1', 'arrayValue2'],
    'mirrored' => Config::getParam('mirrored'),
];
