<?php

namespace Utopia\Config;

use Utopia\Config\Exceptions\Load;
use Utopia\Config\Exceptions\Parse;

class Config
{
    /**
     * @var array<string, mixed>
     */
    public static array $params = [];

    /**
     * Load config file
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     *
     * @throws Load
     * @throws Parse
     */
    public static function load(string $key, string $path, Adapter $adapter): void
    {
        if (! \is_readable($path)) {
            throw new Load('Failed to load configuration file: '.$path);
        }

        self::$params[$key] = $adapter->load($path);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function setParam(string $key, mixed $value): void
    {
        self::$params[$key] = $value;
    }

    /**
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public static function getParam(string $key, mixed $default = null): mixed
    {
        // fast path: no dots means flat key
        if (! str_contains($key, '.')) {
            return self::$params[$key] ?? $default;
        }

        // nested path:
        // foreach instead of array_shift (avoids O(n) reindexing)
        $node = self::$params;
        foreach (\explode('.', $key) as $segment) {
            if (! \is_array($node) || ! isset($node[$segment])) {
                return $default;
            }
            $node = $node[$segment];
        }

        return $node;
    }
}
