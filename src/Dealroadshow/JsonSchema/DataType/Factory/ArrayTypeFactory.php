<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType\Factory;

use Dealroadshow\JsonSchema\DataType\ArrayType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\DataTypesService;

class ArrayTypeFactory implements TypeAwareFactoryInterface
{
    private const EXPECTED_TYPE = 'array';

    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface
    {
        $itemType = $service->determineType($schema['items']);

        return new ArrayType($itemType);
    }

    public function expectedTypes(): array
    {
        return [ self::EXPECTED_TYPE ];
    }
}
