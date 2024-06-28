<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor;

use Nette\PhpGenerator\Property;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property\PropertyDefinitionProcessorInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property\PropertyProcessorInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Nette\PhpGenerator\ClassType;

class PropertyProcessingService
{
    /**
     * @var PropertyDefinitionProcessorInterface[]|iterable
     */
    private $definitionProcessors;

    /**
     * @var PropertyProcessorInterface[]|iterable
     */
    private $propertyProcessors;

    /**
     * @param PropertyDefinitionProcessorInterface[]|iterable $propertyDefinitionsProcessors
     * @param PropertyProcessorInterface[]|iterable $propertyProcessors
     */
    public function __construct(iterable $propertyDefinitionsProcessors, iterable $propertyProcessors)
    {
        $this->definitionProcessors = $propertyDefinitionsProcessors;
        $this->propertyProcessors = $propertyProcessors;
    }

    public function processDefinition(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        foreach ($this->definitionProcessors as $processor) {
            if ($processor->supports($property, $className, $class, $context)) {
                $processor->process($property, $className, $class, $context);
            }
        }
    }

    public function processProperty(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): void
    {
        foreach ($this->propertyProcessors as $processor) {
            if ($processor->supports($definition, $property, $className, $class, $context)) {
                $processor->process($definition, $property, $className, $class, $context);
            }
        }
    }
}
