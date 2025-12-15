<?php

namespace Utopia\Config\Parser;

use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

class JSON extends Parser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $contents): array
    {
        $config = \json_decode($contents, true);

        if (\is_null($config)) {
            throw new Parse('Config file is not a valid JSON file.');
        }

        return $config;
    }
}
