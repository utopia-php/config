<?php

namespace Utopia\Config;

use Utopia\Config\Exception\Parse;

abstract class Adapter
{
    /**
     * @return array<string, mixed>
     *
     * @throws Parse
     */
    abstract public function parse(string $contents): array;
}
