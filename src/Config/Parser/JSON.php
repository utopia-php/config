<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class JSON extends Parser
{
    /**
     * @param \ReflectionClass<covariant object>|null $reflection
     * @return array<string, mixed>
     */
    public function parse(mixed $contents, ?\ReflectionClass $reflection = null): array
    {
        if (!\is_string($contents)) {
            throw new Parse('Contents must be a string.');
        }

        if (empty($contents)) {
            return [];
        }

        $config = \json_decode($contents, true);

        if (\is_null($config) || \json_last_error() !== JSON_ERROR_NONE) {
            throw new Parse('Config file is not a valid JSON file.');
        }

        return $config;
    }
}
