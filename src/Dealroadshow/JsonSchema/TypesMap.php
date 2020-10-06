<?php

namespace Dealroadshow\JsonSchema;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;

class TypesMap implements \IteratorAggregate
{
    /**
     * @var \ArrayObject|array<string, DataTypeInterface>|DataTypeInterface[]
     */
    private \ArrayObject $map;

    /**
     * @param array<string, DataTypeInterface>|DataTypeInterface[] $typesMap
     */
    public function __construct(array $typesMap)
    {
        $this->map = new \ArrayObject($typesMap);
    }

    public function get(string $name): DataTypeInterface
    {
        return $this->map[$name];
    }

    public function has(string $name): bool
    {
        return $this->map->offsetExists($name);
    }

    public function filter(callable $callback): TypesMap
    {
        $newMap = [];
        foreach ($this as $name => $type) {
            if ($callback($type, $name)) {
                $newMap[$name] = $type;
            }
        }

        return new self($newMap);
    }

    public function set(string $name, DataTypeInterface $type): void
    {
        $this->map[$name] = $type;
    }

    /**
     * @return \ArrayObject|array<string, DataTypeInterface>|DataTypeInterface[]
     */
    public function getIterator()
    {
        return $this->map;
    }
}
