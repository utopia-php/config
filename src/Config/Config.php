<?php

namespace Utopia\Config;

use Utopia\Config\Attribute\Key;
use Utopia\Config\Exception\Load;

class Config
{
    /**
     * @var array<Loader>
     */
    protected array $loaders = [];

    /**
     * @param  array<Loader>|Loader  $loaders
     */
    public function __construct(mixed $loaders)
    {
        if (\is_array($loaders)) {
            $this->loaders = $loaders;
        } else {
            $this->loaders = [$loaders];
        }
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $className
     * @return T
     */
    public function load(string $className): mixed
    {
        if (empty($this->loaders)) {
            throw new Load('No loaders specified. Add a loader with addLoader() method');
        }

        $data = [];
        foreach ($this->loaders as $loader) {
            $contents = $loader->getSource()->getContents();
            if ($contents === null) {
                throw new Load('Loader returned null contents.');
            }

            $data = array_merge($data, $loader->getAdapter()->parse($contents));
        }

        $instance = new $className();

        $reflection = new \ReflectionClass($className);
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes(Key::class) as $attribute) {
                $key = $attribute->newInstance();
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
