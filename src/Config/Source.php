<?php

namespace Utopia\Config;

abstract class Source
{
    protected mixed $contents;

    public function getContents(): mixed
    {
        return $this->contents;
    }
}
