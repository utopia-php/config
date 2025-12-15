<?php

namespace Utopia\Config\Attribute;

use Attribute;

#[Attribute]
class ConfigKey
{
    public function __construct(
        public string $name = '',
        public bool $required = true,
    ) {
    }
}
