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
        // TODO: Must be able to parse
        throw new Parse('PHP config only supports loading, not parsing.');
    }
}
