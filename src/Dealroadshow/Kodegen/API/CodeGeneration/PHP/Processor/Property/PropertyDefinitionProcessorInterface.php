<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

interface PropertyDefinitionProcessorInterface
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void;
    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool;
}
