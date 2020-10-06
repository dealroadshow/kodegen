<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

abstract class AbstractPropertyProcessor implements PropertyProcessorInterface
{
    public function process(PropertyDefinition $definition, Property $property, ClassName $className,ClassType $classType, Context $context): void
    {
    }
}