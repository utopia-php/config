<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;

class None extends Parser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(mixed $contents): array
    {
        return $contents;
    }
}
