<?php

namespace Utopia\Config\Source;

use Utopia\Config\Source;

class File extends Source
{
    public function __construct(string $path)
    {
        if (!\file_exists($path)) {
            $this->contents = null;
            return;
        }

        $this->contents = \file_get_contents($path);
        if ($this->contents === false) {
            $this->contents = null;
        }
    }
}
