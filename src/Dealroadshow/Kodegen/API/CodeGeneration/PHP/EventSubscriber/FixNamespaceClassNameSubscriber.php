<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassNameEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FixNamespaceClassNameSubscriber implements EventSubscriberInterface
{
    private const WRONG_CLASS_NAME = 'Namespace';
    private const PROPER_CLASS_NAME = 'KubernetesNamespace';

    public function onClassName(ClassNameEvent $event): void
    {
        $className = $event->getClassName();
        if (self::WRONG_CLASS_NAME === $className->shortName()) {
            $className = ClassName::fromNamespaceAndName(
                $className->namespace(),
                self::PROPER_CLASS_NAME
            );
            $event->setClassName($className);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ClassNameEvent::class => 'onClassName',
        ];
    }
}
