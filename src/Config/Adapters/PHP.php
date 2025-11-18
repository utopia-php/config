<?php

namespace Utopia\Config\Adapters;

use Utopia\Config\Adapter;
use Utopia\Config\Exceptions\Parse;

class PHP extends Adapter
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $contents): array
    {
        throw new Parse('PHP config only supports loading, not parsing.');
    }

    /**
     * Overrides native behaviour as parsing and loading are same step
     *
     * @return array<string, mixed>
     */
    public function load(string $path): array
    {
        $contents = include $path;

        if (! \is_array($contents)) {
            throw new \Exception('PHP config did not return any contents.');
        }

        return (array) $contents;
    }
}
