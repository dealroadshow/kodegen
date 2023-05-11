<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType;

final class ArrayType extends AbstractType
{
    public const NAME = 'array';

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
