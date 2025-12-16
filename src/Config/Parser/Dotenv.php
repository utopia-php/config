<?php

namespace Utopia\Config\Parser;

use ReflectionAttribute;
use Utopia\Config\Attribute\Key;
use Utopia\Config\Parser;
use Utopia\Config\Exception\Parse;

// TODO: Once important, handle quoted values to allow symbol # in values
class Dotenv extends Parser
{
    /**
     * @var array<string> $truthyValues
     */
    protected array $truthyValues = ['1', 'true', 'yes', 'on', 'enabled'];

    /**
     * @var array<string> $falsyValues
     */
    protected array $falsyValues = ['0', 'false', 'no', 'off', 'disabled'];

    /**
     * @return string|bool|null
     */
    protected function convertValue(string $value, string $type): mixed
    {
        if ($type === 'bool') {
            if (\in_array(\strtolower($value), $this->truthyValues)) {
                return true;
            }
            if (\in_array(\strtolower($value), $this->falsyValues)) {
                return false;
            }
        }

        return $value;
    }

    /**
     * @param \ReflectionClass<covariant object>|null $reflection
     * @return array<string, mixed>
     */
    public function parse(mixed $contents, ?\ReflectionClass $reflection = null): array
    {
        if (!\is_string($contents)) {
            throw new Parse('Contents must be a string.');
        }

        if (empty($contents)) {
            return [];
        }

        $config = [];

        $lines = \explode("\n", $contents);
        foreach ($lines as $line) {
            // Remove everything after #
            $pair = \strstr($line, '#', true);
            if ($pair === false) {
                $pair = $line;
            }
            $pair = \trim($pair);

            // Empty line can be ignored (after removing comments)
            if (empty($pair)) {
                continue;
            }

            // Split into KEY=value
            $parts = \explode('=', $pair, 2);
            $name = \trim($parts[0]);
            $value = \trim($parts[1] ?? '');

            // Missing name likely means bad syntax
            if (empty($name)) {
                throw new Parse('Config file is not a valid dotenv file.');
            }

            // Smart type-casting
            if ($reflection !== null) {
                $reflectionProperty = null;
                foreach ($reflection->getProperties() as $property) {
                    foreach ($property->getAttributes(Key::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                        $key = $attribute->newInstance();
                        if ($key->name === $name) {
                            $reflectionProperty = $property;
                            break 2;
                        }
                    }
                }
                if ($reflectionProperty !== null) {
                    $reflectionType = $reflectionProperty->getType();
                    if ($reflectionType !== null && \method_exists($reflectionType, 'getName')) {
                        $value = $this->convertValue($value, $reflectionType->getName());
                    }
                }
            }

            if (\is_string($value) && \strtolower($value) === "null") {
                $value = null;
            }

            $config[$name] = $value;
        }

        return $config;
    }
}
