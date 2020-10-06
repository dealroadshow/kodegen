<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassGenerationEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGeneratedEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\PHPClassEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

class DataClassGenerator extends AbstractGenerator
{
    private const NAMESPACE_PREFIX = 'Data';

    private DataClassGenerationEvent $generationEvent;

    public function generate(string $definitionName, ObjectType $type, Context $context): PHPClass
    {
        $namespaceName = $context->namespacePrefix().'\\'.self::NAMESPACE_PREFIX;
        $className = ClassName::fromDefinitionName($namespaceName, $definitionName);
        $this->generationEvent = new DataClassGenerationEvent($className, $context, $type);

        return $this->doGenerate(
            $className,
            $type->description(),
            $type->properties(),
            $context
        );
    }

    protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName
    {
        return ClassName::fromFQCN(\JsonSerializable::class);
    }

    protected function createGenerationEvent(): ClassGenerationEventInterface
    {
        return $this->generationEvent;
    }

    protected function createPHPClassEvent(PHPClass $class): PHPClassEventInterface
    {
        return new DataClassGeneratedEvent($class);
    }
}
