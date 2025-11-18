<?php

namespace Utopia\Config\Adapters;

use Utopia\Config\Adapter;
use Utopia\Config\Exceptions\Parse;

class Dotenv extends Adapter
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $contents): array
    {
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

            $config[$name] = $value;
        }

        return $config;
    }
}
