<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class YAML extends Parser
{
    /**
     * @param \ReflectionClass<covariant object>|null $reflection
     * @return array<string, mixed>
     */
    public function parse(mixed $contents, ?\ReflectionClass $reflection = null): array
    {
        if (!\is_string($contents)) {
            throw new Parse("Contents must be a string.");
        }

        if (empty($contents)) {
            return [];
        }

        $config = null;
        try {
            $config = \yaml_parse($contents);
        } catch (\Throwable $e) {
            throw new Parse(
                "Failed to parse YAML config file: " . $e->getMessage(),
            );
        }

        if ($config === false || $config === null || $config === $contents) {
            throw new Parse("Config file is not a valid YAML file.");
        }

        // TODO: Consider to cast depending on reflection, similar to Dotenv
        // Source: https://softwareengineering.stackexchange.com/questions/387827/is-it-wrong-to-parse-yaml-true-as-a-string

        return $config;
    }
}
