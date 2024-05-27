<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\DataClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class ObjectTypeResolver extends AbstractTypeResolver
{
    public function __construct(private DataClassGenerator $dataClassGenerator)
    {
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof ObjectType && 0 < \count($type->properties());
    }

    public function resolve(
        DataTypeInterface|ObjectType $type,
        PHPTypesService $service,
        Context $context,
        bool $nullable,
        array $runtimeParams
    ): PHPType {
        /** @var ClassName $parentClassName */
        $parentClassName = $runtimeParams['className'];
        /** @var PropertyDefinition $propertyDefinition */
        $propertyDefinition = $runtimeParams['propertyDefinition'];

        $shortName = $parentClassName->shortName().ucwords($propertyDefinition->name());
        $className = ClassName::fromNamespaceAndName($parentClassName->namespace(), $shortName);

        $class = $this->dataClassGenerator->generateFromClassName($className, $type, $context);

        return new PHPType($class->name()->fqcn(), $class->name()->fqcn(), $nullable);
    }
}
