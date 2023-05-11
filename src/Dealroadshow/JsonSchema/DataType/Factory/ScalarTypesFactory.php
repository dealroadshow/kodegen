<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType\Factory;

use Dealroadshow\JsonSchema\DataType\BoolType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\DataTypesService;
use Dealroadshow\JsonSchema\DataType\IntegerType;
use Dealroadshow\JsonSchema\DataType\NumberType;
use Dealroadshow\JsonSchema\DataType\StringType;

class ScalarTypesFactory implements TypeAwareFactoryInterface
{
    private const JSON_SCHEMA_TYPES_MAP = [
        'boolean' => BoolType::class,
        'integer' => IntegerType::class,
        'number'  => NumberType::class,
        'string'  => StringType::class,
    ];

    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface
    {
        $class = self::JSON_SCHEMA_TYPES_MAP[$schema['type']];

        return new $class();
    }

    public function expectedTypes(): array
    {
        return \array_keys(self::JSON_SCHEMA_TYPES_MAP);
    }
}
