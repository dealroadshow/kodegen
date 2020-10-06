<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\BoolType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\IntegerType;
use Dealroadshow\JsonSchema\DataType\NumberType;
use Dealroadshow\JsonSchema\DataType\StringType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class ScalarTypesResolver extends AbstractTypeResolver
{
    private const TYPES_MAP = [
        BoolType::class => PHPType::BOOL,
        IntegerType::class => PHPType::INT,
        NumberType::class => PHPType::FLOAT,
        StringType::class => PHPType::STRING,
    ];

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        $phpType = self::TYPES_MAP[\get_class($type)];
        $docType = $nullable ? $phpType.'|null' : $phpType;

        return new PHPType($phpType, $docType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return \array_key_exists(\get_class($type), self::TYPES_MAP);
    }
}
