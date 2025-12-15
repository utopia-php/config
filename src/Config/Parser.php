<?php

namespace Utopia\Config;

use Utopia\Config\Exception\Parse;

abstract class Parser
{
    /**
     * @return array<string, mixed>
     *
     * @throws Parse
     */
    abstract public function parse(mixed $contents): array;
}
