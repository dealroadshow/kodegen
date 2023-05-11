<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\DataClassGenerationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MetadataAndSpecSubscriber implements EventSubscriberInterface
{
    private const PROPERTY_METADATA = 'metadata';
    private const PROPERTY_SPEC = 'spec';

    public function onAPIClassGeneration(APIClassGenerationEvent $event): void
    {
        $properties = $event->definition()->properties();
        $this->requireMetaAndSpec($properties);
    }

    public function onDataClassGeneration(DataClassGenerationEvent $event): void
    {
        $properties = $event->objectType()->properties();
        $this->requireMetaAndSpec($properties);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGenerationEvent::class => 'onAPIClassGeneration',
            DataClassGenerationEvent::class => 'onDataClassGeneration',
        ];
    }

    /**
     * @param PropertyDefinition[]|array<string, PropertyDefinition> $properties
     */
    private function requireMetaAndSpec(array $properties): void
    {
        foreach ([self::PROPERTY_METADATA, self::PROPERTY_SPEC] as $propertyName) {
            if (!\array_key_exists($propertyName, $properties)) {
                return;
            }
        }

        $properties[self::PROPERTY_METADATA]->setRequired(true);
        $properties[self::PROPERTY_SPEC]->setRequired(true);
    }
}
