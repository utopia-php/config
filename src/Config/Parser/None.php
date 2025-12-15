<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Exception\Parse;
use Utopia\Config\Parser;

class None extends Parser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(mixed $contents): array
    {
        if (!is_array($contents)) {
            throw new Parse('Contents must be an array');
        }

        return $contents;
    }
}
