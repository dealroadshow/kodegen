<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\AbstractUnionType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class UnionTypeResolver extends AbstractTypeResolver
{
    public function resolve(DataTypeInterface|AbstractUnionType $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $types = $type->types();
        $union = [];
        foreach ($types as $underlyingType) {
            $phpType = $service->resolveType($underlyingType, $context, false);
            $union[] = $phpType->name;
        }
        $union = \array_unique($union);
        if (!in_array('null', $union) && $nullable) {
            $union[] = 'null';
        }
        $unionType = \implode('|', $union);

        return new PHPType($unionType, $unionType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof AbstractUnionType;
    }
}
