<?php

use Utopia\Config\Config;

// Comment line and few empty lines, ensure parser doesnt break
// 

return [
    'key' => 'keyValue',
    'keyWithComment' => 'keyWithCommentValue', // A comment
    'nested' => ['key' => 'nestedKeyValue'],
    'array' => ['arrayValue1', 'arrayValue2'],
    'mirrored' => Config::getParam('mirrored'),
];
