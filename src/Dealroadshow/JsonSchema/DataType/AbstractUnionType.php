<?php

namespace Dealroadshow\JsonSchema\DataType;

abstract class AbstractUnionType extends AbstractType
{
    /**
     * @var array|DataTypeInterface[]
     */
    protected array $types;

    /**
     * @param DataTypeInterface[]|array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return array|DataTypeInterface[]
     */
    public function types(): array
    {
        return $this->types;
    }
}
