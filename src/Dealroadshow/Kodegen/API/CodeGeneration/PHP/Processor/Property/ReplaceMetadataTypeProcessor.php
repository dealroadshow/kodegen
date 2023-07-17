<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\JsonSchema\DataType\ReferenceType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GeneratedClassesCache;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class ReplaceMetadataTypeProcessor extends AbstractPropertyDefinitionProcessor
{
    public function __construct(private readonly string|null $metadataClass, private readonly GeneratedClassesCache $cache)
    {
    }

    public function process(
        PropertyDefinition $property,
        ClassName $className,
        ClassType $class,
        Context $context
    ): void {
        $metadataClass = $this->metadataClass;
        $className = ClassName::fromFQCN($metadataClass);

        $property->setNullable(false);
        $property->phpType = new PHPType($metadataClass, $metadataClass, false);

        $initializer = function (Property $property, Method $constructor) use ($className): void {
            $property
                ->setNullable(false)
                ->setInitialized(false);

            $propertyValue = sprintf('new %s()', $className->shortName());

            $constructor->addBody(
                sprintf('$this->%s = %s;', $property->getName(), $propertyValue)
            );
        };

        $property->setInitializer($initializer);

        $phpClass = new PHPClass($className, new ClassType(), true);
        $this->cache->set($metadataClass, $phpClass);
    }

    public function supports(
        PropertyDefinition $property,
        ClassName $className,
        ClassType $class,
        Context $context
    ): bool {
        return 'metadata' === $property->name() && $property->type() instanceof ReferenceType && null !== $this->metadataClass;
    }
}
