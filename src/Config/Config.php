<?php

namespace Utopia\Config;

use Utopia\Config\Attribute\Key;
use Utopia\Config\Exception\Load;

class Config
{
    /**
     * @template T of object
     *
     * @param  class-string<T>  $className
     * @return T
     */
    public static function load(Source $source, Parser $parser, string $className): mixed
    {
        $contents = $source->getContents();
        if ($contents === null) {
            throw new Load('Loader returned null contents.');
        }

        $data = $parser->parse($contents);

        $instance = new $className();

        $reflection = new \ReflectionClass($className);

        if (\count($reflection->getMethods()) > 0) {
            throw new Load("Class {$className} cannot have any functions.");
        }

        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes(Key::class) as $attribute) {
                $key = $attribute->newInstance();

                if(!$key->required && !\array_key_exists($key->name, $data)) {
                    continue;
                }
                
                $value = $data[$key->name] ?? null;

                if ($key->required && $value === null) {
                    throw new Load("Missing required key: {$key->name}");
                }

                if (! $key->validator->isValid($value)) {
                    throw new Load("Invalid value for {$key->name}: {$key->validator->getDescription()}");
                }

                $propertyName = $property->name;
                $instance->$propertyName = $value;
            }
        }

        return $instance;
    }
}
