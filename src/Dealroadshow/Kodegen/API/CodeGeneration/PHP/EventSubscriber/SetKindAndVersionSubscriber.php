<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\JsonSerializeMethodEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetKindAndVersionSubscriber implements EventSubscriberInterface
{
    use ProcessedClassesTrait;

    private const PROPERTY_API_VERSION = 'apiVersion';
    private const PROPERTY_KIND = 'kind';
    private const CONSTANT_API_VERSION = 'API_VERSION';
    private const CONSTANT_KIND = 'KIND';

    private array $map = [];

    public function onAPIClassGeneration(APIClassGenerationEvent $event): void
    {
        $className = $event->className();
        $definition = $event->definition();
        if ($this->isProcessed($className)) {
            return;
        }

        $properties = $definition->properties();
        $properties[self::PROPERTY_API_VERSION]->skip();
        $properties[self::PROPERTY_KIND]->skip();

        $this->map[$className->fqcn()] = [
            self::PROPERTY_API_VERSION => $definition->apiVersion(),
            self::PROPERTY_KIND => $definition->kind(),
        ];

        $this->markAsProcessed($className);
    }

    public function onJsonSerializeMethodGeneration(JsonSerializeMethodEvent $event): void
    {
        $className = $event->className();
        if (!array_key_exists($className->fqcn(), $this->map)) {
            return;
        }

        $class = $event->classType();
        $class->addConstant(
            self::CONSTANT_API_VERSION,
            $this->apiVersionForClass($className)
        );
        $class->addConstant(self::CONSTANT_KIND, $this->kindForClass($className));

        $event->addJsonProperty(
            'apiVersion',
            sprintf('self::%s', self::CONSTANT_API_VERSION)
        );
        $event->addJsonProperty(
            'kind',
            sprintf('self::%s', self::CONSTANT_KIND)
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGenerationEvent::class => 'onAPIClassGeneration',
            JsonSerializeMethodEvent::class => 'onJsonSerializeMethodGeneration',
        ];
    }

    private function apiVersionForClass(ClassName $className)
    {
        return $this->map[$className->fqcn()][self::PROPERTY_API_VERSION];
    }

    private function kindForClass(ClassName $className)
    {
        return $this->map[$className->fqcn()][self::PROPERTY_KIND];
    }
}
