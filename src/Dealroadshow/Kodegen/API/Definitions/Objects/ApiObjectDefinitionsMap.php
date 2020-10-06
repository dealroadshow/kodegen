<?php

namespace Dealroadshow\Kodegen\API\Definitions\Objects;

use Dealroadshow\JsonSchema\TypesMap;

class ApiObjectDefinitionsMap implements \IteratorAggregate
{
    /**
     * @var \ArrayObject|array<string, ApiObjectDefinition>|ApiObjectDefinition[]
     */
    private \ArrayObject $definitionsMap;

    private function __construct(array $definitionsMap)
    {
        $this->definitionsMap = new \ArrayObject($definitionsMap);
    }

    public function get(string $name): ApiObjectDefinition
    {
        return $this->definitionsMap[$name];
    }

    public function has(string $name): bool
    {
        return $this->definitionsMap->offsetExists($name);
    }

    /**
     * @return \ArrayObject|array<string, ApiObjectDefinition>|ApiObjectDefinition[]
     */
    public function getIterator()
    {
        return $this->definitionsMap;
    }

    public static function fromTypesMap(TypesMap $typesMap): self
    {
        $map = [];
        foreach ($typesMap as $name => $type) {
            $map[$name] = ApiObjectDefinition::fromObjectType($name, $type);
        }

        return new self($map);
    }

    public static function fromArray(array $definitionsMap): self
    {
        return new self($definitionsMap);
    }
}
