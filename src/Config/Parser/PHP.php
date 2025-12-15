<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class PHP extends Parser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(mixed $contents): array
    {
        if (!\is_string($contents)) {
            throw new Parse('Contents must be a string.');
        }

        $tempPath = \tempnam(\sys_get_temp_dir(), "utopia_config_");
        if ($tempPath === false) {
            throw new Parse("Failed to create temporary file for PHP config.");
        }

        if (\file_put_contents($tempPath, $contents) === false) {
            throw new Parse("Failed to write PHP config to temporary file.");
        }

        $contents = include $tempPath;
        unlink($tempPath);

        if (!\is_array($contents)) {
            throw new Parse("PHP config file must return an array.");
        }

        return $contents;
    }
}
