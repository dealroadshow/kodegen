<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Nette\PhpGenerator\Property;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Nette\PhpGenerator\ClassType;

interface PropertyProcessorInterface
{
    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void;
    public function supports(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): bool;
}
