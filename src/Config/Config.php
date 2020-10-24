<?php

namespace Utopia\Config;

use Exception;

class Config
{
    /**
     * @var array
     */
    static $params = [];

    /**
     * Load config file
     * 
     * @throws Exception
     * 
     * @return void
     */
    static public function load(string $key, string $path): void
    {
        if(!\is_readable($path)) {
            throw new Exception('Failed to load configuration file: '.$path);
        }
        
        self::$params[$key] = include $path;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    static public function setParam($key, $value): void
    {
        self::$params[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    static public function getParam(string $key, $default = null)
    {
        $key = \explode('.', $key);
        $value = $default;
        $node = self::$params;
        
        while(!empty($key)) {
            $path = \array_shift($key);
            $value = (isset($node[$path])) ? $node[$path] : $default;
            
            if(isset($node[$path]) && \is_array($value)) {
                $node = $node[$path];
            }
        }
        
        return $value;
    }
}
