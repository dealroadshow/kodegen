<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\BoolType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\IntegerType;
use Dealroadshow\JsonSchema\DataType\NumberType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class ScalarResolver extends AbstractTypeResolver
{
    private const TYPES_MAP = [
        BoolType::class => PHPType::BOOL,
        IntegerType::class => PHPType::INT,
        NumberType::class => PHPType::FLOAT,
    ];

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $phpType = $this->phpType($type);
        if ($nullable) {
            $phpType .= '|null';
        }

        return new PHPType($phpType, $phpType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return \array_key_exists($type::class, self::TYPES_MAP);
    }

    protected function phpType(DataTypeInterface $type): string
    {
        return self::TYPES_MAP[$type::class];
    }
}
