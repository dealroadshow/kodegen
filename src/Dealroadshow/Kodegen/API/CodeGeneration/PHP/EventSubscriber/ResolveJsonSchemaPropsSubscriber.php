<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResolveJsonSchemaPropsSubscriber implements EventSubscriberInterface
{
    private const CLASS_NAME = 'JSONSchemaProps';
    private const PRIORITY = 2;

    public function onDataClassGeneration(DataClassGenerationEvent $event): void
    {
        $className = $event->className();
        if (self::CLASS_NAME !== $className->shortName()) {
            return;
        }

        $class = new ClassType($className->shortName());
        $class->setInterface();

        $phpClass = new PHPClass($className, $class);
        $event->setClass($phpClass);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataClassGenerationEvent::class => ['onDataClassGeneration', self::PRIORITY],
        ];
    }
}
