<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\DataType\ReferenceType;
use Dealroadshow\JsonSchema\DataType\UnknownType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\APIClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\DataClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObjectsService;

class ReferenceTypeResolver extends AbstractTypeResolver
{
    private APIClassGenerator $apiClassGenerator;
    private DataClassGenerator $dataClassGenerator;
    private ValueObjectsService $valueObjectsService;

    public function __construct(APIClassGenerator $apiClassGenerator, DataClassGenerator $dataClassGenerator, ValueObjectsService $valueObjectsService)
    {
        $this->apiClassGenerator = $apiClassGenerator;
        $this->dataClassGenerator = $dataClassGenerator;
        $this->valueObjectsService = $valueObjectsService;
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof ReferenceType;
    }

    /**
     * @param DataTypeInterface|ReferenceType $type
     * @param PHPTypesService                 $service
     * @param Context                         $context
     * @param bool                            $nullable
     *
     * @return PHPType
     */
    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        $definitions = $context->definitions();
        $types = $context->types();

        $definitionName = $type->referencedDefinitionName();

        if ($definitions->has($definitionName)) {
            $definition = $definitions->get($definitionName);
            $class = $this->apiClassGenerator->generate($definition, $context);
        } elseif ($types->has($definitionName)) {
            $type = $types->get($definitionName);
            if ($type instanceof ObjectType) {
                $class = $this->dataClassGenerator->generate($definitionName, $type, $context);
            } elseif ($type instanceof UnknownType) {
                return new PHPType(null, 'mixed', false);
            } else {
                $class = $this->valueObjectsService->generateVOClass($definitionName, $type, $context);
            }
        } else {
            throw new \LogicException(
                sprintf('Unknown definition name "%s"', $definitionName)
            );
        }

        if (
            $class->classType()->hasMethod('__construct')
            && ($method = $class->classType()->getMethod('__construct'))
            && 0 === count($method->getParameters())
        ) {
            // See ReferencePropertyProcessor class
            // TODO this should not be here. Think where to put this logic
            $nullable = false;
        }

        $docType = $nullable ? $class->name()->fqcn().'|null' : $class->name()->fqcn();

        return new PHPType($class->name()->fqcn(), $docType, $nullable);
    }
}
