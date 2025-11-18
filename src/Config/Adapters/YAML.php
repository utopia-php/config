<?php

namespace Utopia\Config\Adapters;

use Utopia\Config\Adapter;
use Utopia\Config\Exceptions\Parse;

class YAML extends Adapter
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $contents): array
    {
        $config = \yaml_parse($contents);

        if ($config === false) {
            throw new Parse('Config file is not a valid YAML file.');
        }

        return $config;
    }
}
