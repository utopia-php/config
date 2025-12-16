<?php

namespace Utopia\Config;

use Utopia\Config\Exception\Parse;

abstract class Parser
{
    /**
     * @param \ReflectionClass<covariant object>|null $reflection Useful for loosely typed parsers (like Dotenv) to cast smartly to correct types
     * @return array<string, mixed>
     * @throws Parse
     */
    abstract public function parse(mixed $contents, ?\ReflectionClass $reflection = null): array;
}
