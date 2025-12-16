<?php

namespace Utopia\Config\Source;

use Utopia\Config\Source;

class Environment extends Source
{
    public function __construct()
    {
        $this->contents = \getenv();
    }
}
