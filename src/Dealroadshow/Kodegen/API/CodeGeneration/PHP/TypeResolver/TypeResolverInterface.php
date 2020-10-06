<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

interface TypeResolverInterface
{
    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType;
    public function supports(DataTypeInterface $type): bool;
}
