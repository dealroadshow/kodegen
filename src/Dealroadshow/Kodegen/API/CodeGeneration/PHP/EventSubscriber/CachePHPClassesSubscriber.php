<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGeneratedEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassGenerationEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGeneratedEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\PHPClassEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GeneratedClassesCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CachePHPClassesSubscriber implements EventSubscriberInterface
{
    private GeneratedClassesCache $cache;

    public function __construct(GeneratedClassesCache $cache)
    {
        $this->cache = $cache;
    }

    public function onClassGeneration(ClassGenerationEventInterface $event)
    {
        $className = $event->className();
        $fcqn = $className->fqcn();
        if ($event->hasClass()) {
            $this->cache->set($fcqn, $event->getClass());
        }
        if ($this->cache->has($fcqn)) {
            $event->setClass($this->cache->get($fcqn));
        }
    }

    public function onClassGenerated(PHPClassEventInterface $event)
    {
        $class = $event->getClass();
        $this->cache->set(
            $class->name()->fqcn(),
            $class
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            APIClassGenerationEvent::class => 'onClassGeneration',
            DataClassGenerationEvent::class => 'onClassGeneration',
            APIClassGeneratedEvent::class => 'onClassGenerated',
            DataClassGeneratedEvent::class => 'onClassGenerated',
        ];
    }
}
