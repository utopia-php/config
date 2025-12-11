<?php

namespace Utopia\Config;

use Utopia\Config\Exceptions\Parse;

abstract class Adapter
{
    /**
     * @return array<string, mixed>
     *
     * @throws Parse
     */
    abstract public function parse(string $contents): array;
}
