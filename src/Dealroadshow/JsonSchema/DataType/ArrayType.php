<?php

namespace Dealroadshow\JsonSchema\DataType;

final class ArrayType extends AbstractType
{
    const NAME = 'array';

    private DataTypeInterface $itemType;

    public function __construct(DataTypeInterface $itemType)
    {
        $this->itemType = $itemType;
    }

    public function itemType(): DataTypeInterface
    {
        return $this->itemType;
    }
}
