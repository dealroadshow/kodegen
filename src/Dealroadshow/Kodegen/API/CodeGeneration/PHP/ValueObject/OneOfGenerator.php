<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObject;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\OneOfType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Nette\PhpGenerator\ClassType;

class OneOfGenerator extends AbstractGenerator
{
    public function generate(ClassName $className, DataTypeInterface|OneOfType $type, Context $context, PHPTypesService $service): PHPClass
    {
        $class = new ClassType($className->shortName());
        $phpType = $service->resolveType($type, $context, false);

        $this
            ->defineProperty($class, $phpType)
            ->defineConstructor($class, $phpType);

        foreach ($type->types() as $type) {
            $phpType = $service->resolveType($type, $context, false);
            $this->defineFactoryMethod($class, $phpType);
        }

        $phpClass = new PHPClass($className, $class);
        $this->defineJsonSerializeMethod($phpClass);

        return $phpClass;
    }

    public function supports(ClassName $className, DataTypeInterface $type): bool
    {
        return $type instanceof OneOfType;
    }
}
