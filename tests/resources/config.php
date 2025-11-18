<?php

use Utopia\Config\Config;

return [
    'key' => 'keyValue',
    'keyWithComment' => 'keyWithCommentValue', // A comment
    'nested' => ['key' => 'nestedKeyValue'],
    'array' => ['arrayValue1', 'arrayValue2'],
    'mirrored' => Config::getParam('mirrored'),
];
