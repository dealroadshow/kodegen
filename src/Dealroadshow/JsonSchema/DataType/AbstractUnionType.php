<?php

namespace Dealroadshow\JsonSchema\DataType;

abstract class AbstractUnionType extends AbstractType
{
    /**
     * @var DataTypeInterface[]
     */
    protected array $types;

    /**
     * @param DataTypeInterface[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return DataTypeInterface[]
     */
    public function types(): array
    {
        return $this->types;
    }
}
