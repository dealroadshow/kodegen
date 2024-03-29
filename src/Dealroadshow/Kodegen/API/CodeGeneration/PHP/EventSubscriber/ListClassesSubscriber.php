<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\AbstractPHPClassEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGeneratedEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGeneratedEvent;

class ListClassesSubscriber implements EventSubscriberInterface
{
    public function onClassGenerated(AbstractPHPClassEvent $event): void
    {
        $class = $event->getClass()->classType();

        if (!str_ends_with($class->getName(), 'List') || !$class->hasProperty('items')) {
            return;
        }

        $class
            ->removeMethod('getItems')
            ->removeMethod('setItems');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGeneratedEvent::class => 'onClassGenerated',
            DataClassGeneratedEvent::class => 'onClassGenerated',
        ];
    }
}
