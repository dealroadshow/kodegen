<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\ArrayType;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\ListClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\MapClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class CollectionTypeResolver implements TypeResolverInterface
{
    private MapClassGenerator $mapGenerator;
    private ListClassGenerator $listGenerator;

    public function __construct(MapClassGenerator $generator, ListClassGenerator $listGenerator)
    {
        $this->mapGenerator = $generator;
        $this->listGenerator = $listGenerator;
    }

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        $itemPHPType = $this->resolveItemType($type, $service, $context);
        $generator = $type instanceof ObjectType ? $this->mapGenerator : $this->listGenerator;
        $phpClass = $generator->generate($itemPHPType, $context);
        $className = $phpClass->name();

        return new PHPType($className->fqcn(), $className->fqcn(), false);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return ($type instanceof ObjectType && 0 === \count($type->properties()))
               || $type instanceof ArrayType;
    }

    protected function resolveItemType(DataTypeInterface $type, PHPTypesService $service, Context $context): PHPType
    {
        if ($type instanceof ObjectType) {
            $itemType = $type->additionalPropertiesType();
        } elseif ($type instanceof ArrayType) {
            $itemType = $type->itemType();
        } else {
            throw new \LogicException(
                \sprintf(
                    '%s expects either %s or %s, %s given',
                    __CLASS__,
                    ObjectType::class,
                    ArrayType::class,
                    \get_class($type)
                )
            );
        }

        return $service->resolveType($itemType, $context, false);
    }
}
