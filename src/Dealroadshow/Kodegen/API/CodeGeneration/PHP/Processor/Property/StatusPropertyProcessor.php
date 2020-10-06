<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;


use Nette\PhpGenerator\ClassType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

class StatusPropertyProcessor extends AbstractPropertyDefinitionProcessor
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        $property->skip();
    }

    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool
    {
        return 'status' === $property->name();
    }
}
