<?php

use Utopia\Config\Config;

$child = Config::getParam('child', 'childValue');

return [
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => $child,
];