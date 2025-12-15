<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class YAML extends Parser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(mixed $contents): array
    {
        if (!\is_string($contents)) {
            throw new Parse('Contents must be a string.');
        }

        if (empty($contents)) {
            return [];
        }

        $config = \yaml_parse($contents);

        if ($config === false || $config === null) {
            throw new Parse('Config file is not a valid YAML file.');
        }

        return $config;
    }
}
