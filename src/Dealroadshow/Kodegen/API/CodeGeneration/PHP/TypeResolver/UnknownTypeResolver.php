<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\UnknownType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class UnknownTypeResolver extends AbstractTypeResolver
{

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        return new PHPType(null, 'mixed', false);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof UnknownType;
    }
}
