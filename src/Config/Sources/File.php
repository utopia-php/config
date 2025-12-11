<?php

namespace Utopia\Config\Sources;

use Utopia\Config\Source;

class File extends Source
{
    public function __construct(string $path)
    {
        $this->contents = \file_get_contents($path);
    }
}
