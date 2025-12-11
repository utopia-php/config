<?php

namespace Utopia\Config\Sources;

use Utopia\Config\Source;

class Variable extends Source
{
    public function __construct(mixed $contents)
    {
        $this->contents = $contents;
    }
}
