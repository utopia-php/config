<?php

namespace Utopia\Config\Attributes;

use Attribute;
use Utopia\Validator;

#[Attribute]
class Key
{
    public function __construct(
        public string $name,
        public Validator $validator,
        public bool $required,
    ) {
    }
}
