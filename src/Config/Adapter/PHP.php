<?php

namespace Utopia\Config\Adapter;

use Utopia\Config\Adapter;

class PHP extends Adapter
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $contents): array
    {
        $tempPath = \tempnam(\sys_get_temp_dir(), 'utopia_config_');
        file_put_contents($tempPath, $contents);
        $contents = include $tempPath;
        unlink($tempPath);
        return $contents;
    }
}
