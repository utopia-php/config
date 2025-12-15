<?php

namespace Utopia\Config;

use ReflectionAttribute;
use Utopia\Config\Attribute\ConfigKey;
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
            $attributeFound = false;

            foreach ($property->getAttributes(Key::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $attributeFound = true;
                $key = $attribute->newInstance();

                if (!$key->required && !\array_key_exists($key->name, $data)) {
                    continue;
                }

                $value = $data[$key->name] ?? null;

                if ($key->required && $value === null) {
                    throw new Load("Missing required key: {$key->name}");
                }

                $expectedType = $property->getType();
                if ($expectedType === null) {
                    throw new Load("Property {$property->name} is missing a type.");
                }

                if (! $key->validator->isValid($value)) {
                    throw new Load("Invalid value for {$key->name}: {$key->validator->getDescription()}");
                }

                $propertyName = $property->name;
                $instance->$propertyName = $value;
            }

            foreach ($property->getAttributes(ConfigKey::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $attributeFound = true;

                $key = $attribute->newInstance();
                $keyName = $key->name;
                if (empty($keyName)) {
                    $keyName = $property->name;
                }

                if (!$key->required && !\array_key_exists($keyName, $data)) {
                    continue;
                }

                $value = $data[$keyName] ?? null;

                if ($key->required && $value === null) {
                    throw new Load("Missing required key: {$keyName}");
                }

                $expectedType = $property->getType();
                if ($expectedType === null || !\method_exists($expectedType, 'getName')) {
                    throw new Load("Property {$property->name} is missing a type.");
                }

                $expectedClass = $expectedType->getName();

                if (!($value instanceof $expectedClass)) {
                    throw new Load("Invalid value for {$keyName}: Must be instance of {$expectedClass}.");
                }

                $propertyName = $property->name;
                $instance->$propertyName = $value;
            }

            if (!$attributeFound) {
                throw new Load("Property {$property->name} is missing attribute syntax.");
            }
        }

        return $instance;
    }
}
