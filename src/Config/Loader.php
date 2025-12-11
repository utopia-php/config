<?php

namespace Utopia\Config;

class Loader
{
    public function __construct(protected Source $source, protected Adapter $adapter)
    {
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }
}
