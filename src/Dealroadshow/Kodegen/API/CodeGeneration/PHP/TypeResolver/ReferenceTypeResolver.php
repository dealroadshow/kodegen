<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\DataType\ReferenceType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\APIClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\DataClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class ReferenceTypeResolver extends AbstractTypeResolver
{
    public function __construct(private APIClassGenerator $apiClassGenerator, private DataClassGenerator $dataClassGenerator)
    {
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof ReferenceType;
    }

    public function resolve(
        DataTypeInterface|ReferenceType $type,
        PHPTypesService $service,
        Context $context,
        bool $nullable,
        array $runtimeParams
    ): PHPType {
        $definitions = $context->definitions;
        $types = $context->types;

        $definitionName = $type->referencedDefinitionName();

        if ($definitions->has($definitionName)) {
            $definition = $definitions->get($definitionName);
            $class = $this->apiClassGenerator->generate($definition, $context);
        } elseif ($types->has($definitionName)) {
            $type = $types->get($definitionName);
            if (!$type instanceof ObjectType) {
                return $service->resolveType($type, $context, $nullable);
            }
            $class = $this->dataClassGenerator->generateFromDefinitionName($definitionName, $type, $context);
        } else {
            throw new \LogicException(
                sprintf('Unknown definition name "%s"', $definitionName)
            );
        }

        return new PHPType($class->name()->fqcn(), $class->name()->fqcn(), $nullable);
    }
}
