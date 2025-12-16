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

        $reflection = new \ReflectionClass($className);

        $data = $parser->parse($contents, $reflection);

        $instance = new $className();

        if (\count($reflection->getMethods()) > 0) {
            throw new Load("Class {$className} cannot have any functions.");
        }

        foreach ($reflection->getProperties() as $property) {
            $attributeFound = false;

            foreach ($property->getAttributes(Key::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $attributeFound = true;
                $key = $attribute->newInstance();

                $value = self::resolveValue($data, $key->name);

                if (!$key->required && $value === null) {
                    continue;
                }

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

                $value = self::resolveValue($data, $keyName);

                if (!$key->required && $value === null) {
                    continue;
                }

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

    /**
     * @param array<string, mixed> $data The data array to search in
     * @return mixed|null The value if found, null otherwise
     */
    protected static function resolveValue(array $data, string $key): mixed
    {
        // Exact match
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        // Dot notation
        $parts = explode('.', $key);
        return self::resolveValueRecursive($data, $parts, 0);
    }

    /**
     * @param array<string, mixed> $data Current data context
     * @param array<int, string> $parts Remaining parts of the key
     * @return mixed|null The value if found, null otherwise
     */
    protected static function resolveValueRecursive(array $data, array $parts, int $index): mixed
    {
        if ($index >= count($parts)) {
            return null;
        }

        if ($index === count($parts) - 1) {
            return array_key_exists($parts[$index], $data) ? $data[$parts[$index]] : null;
        }

        for ($length = 1; $length <= count($parts) - $index; $length++) {
            $keyParts = array_slice($parts, $index, $length);
            $key = implode('.', $keyParts);

            if (array_key_exists($key, $data)) {
                $value = $data[$key];

                if ($index + $length === count($parts)) {
                    return $value;
                }

                if (is_array($value)) {
                    $result = self::resolveValueRecursive($value, $parts, $index + $length);
                    if ($result !== null) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }
}
