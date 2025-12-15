<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class Dotenv extends Parser
{
    /**
     * @var array<string> $truthyValues
     */
    protected array $truthyValues = ['1', 'true', 'yes', 'on', 'enabled'];

    /**
     * @var array<string> $falsyValues
     */
    protected array $falsyValues = ['0', 'false', 'no', 'off', 'disabled'];

    /**
     * @return string|bool|null
     */
    protected function convertValue(string $value): mixed
    {
        if (\strtolower($value) === "null") {
            return null;
        }

        if (\in_array(\strtolower($value), $this->truthyValues)) {
            return true;
        }
        if (\in_array(\strtolower($value), $this->falsyValues)) {
            return false;
        }

        return $value;
    }

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

        $config = [];

        $lines = \explode("\n", $contents);
        foreach ($lines as $line) {
            // Remove everything after #
            $pair = \strstr($line, '#', true);
            if ($pair === false) {
                $pair = $line;
            }
            $pair = \trim($pair);

            // Empty line can be ignored (after removing comments)
            if (empty($pair)) {
                continue;
            }

            // Split into KEY=value
            $parts = \explode('=', $pair, 2);
            $name = \trim($parts[0]);
            $value = \trim($parts[1] ?? '');

            // Missing name likely means bad syntax
            if (empty($name)) {
                throw new Parse('Config file is not a valid dotenv file.');
            }

            $config[$name] = $this->convertValue($value);
        }

        return $config;
    }
}
