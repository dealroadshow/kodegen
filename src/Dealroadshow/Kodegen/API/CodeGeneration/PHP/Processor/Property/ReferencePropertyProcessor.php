<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\JsonSchema\DataType\ReferenceType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GeneratedClassesCache;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

class ReferencePropertyProcessor extends AbstractPropertyProcessor
{
    private GeneratedClassesCache $cache;

    public function __construct(GeneratedClassesCache $cache)
    {
        $this->cache = $cache;
    }

    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void
    {
        $fqcn = $property->getType();

        if (!$this->cache->has($fqcn)) {
            return;
        }

        $phpClass = $this->cache->get($fqcn);

        if ($phpClass->isExternal) {
            return;
        }

        if (!$phpClass->classType()->hasMethod('__construct')) {
            return;
        }

        $method = $phpClass->classType()->getMethod('__construct');
        if (0 !== count($method->getParameters())) {
            return;
        }

        $definition->setNullable(false);
        $property
            ->setNullable(false)
            ->setInitialized(false);
        $initializer = function (Property $property, Method $constructor): void {
            $propertyType = $property->getType();
            $propertyValue = sprintf('new %s()', $propertyType);

            $constructor->addBody(
                sprintf('$this->%s = %s;', $property->getName(), $propertyValue)
            );
        };

        $definition->setInitializer($initializer);
    }

    public function supports(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): bool
    {
        return $definition->type() instanceof ReferenceType;
    }
}
