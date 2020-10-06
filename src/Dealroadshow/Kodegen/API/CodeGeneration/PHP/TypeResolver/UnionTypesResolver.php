<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\AbstractUnionType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class UnionTypesResolver extends AbstractTypeResolver
{
    /**
     * @param DataTypeInterface|AbstractUnionType $type
     * @param PHPTypesService                     $service
     * @param Context                             $context
     * @param bool                                $nullable
     *
     * @return PHPType
     */
    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        $types = $type->types();
        $docTypes = [];
        foreach ($types as $underlyingType) {
            $phpType = $service->resolveType($underlyingType, $context, false);
            $docTypes[] = $phpType->docType();
        }
        if (!in_array('null', $docTypes) && $nullable) {
            $docTypes[] = 'null';
        }
        $docType = \implode('|', $docTypes);

        return new PHPType(null, $docType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof AbstractUnionType;
    }
}
