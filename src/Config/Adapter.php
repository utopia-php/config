<?php

namespace Utopia\Config;

use Utopia\Config\Exceptions\Load;
use Utopia\Config\Exceptions\Parse;

abstract class Adapter
{
    /**
     * @return array<string, mixed>
     *
     * @throws Parse
     */
    abstract public function parse(string $contents): array;

    /**
     * @return array<string, mixed>
     *
     * @throws Load
     */
    public function load(string $path): array
    {
        $contents = \file_get_contents($path);

        if ($contents === false) {
            throw new Load('Config file not found.');
        }

        return $this->parse($contents);
    }
}
