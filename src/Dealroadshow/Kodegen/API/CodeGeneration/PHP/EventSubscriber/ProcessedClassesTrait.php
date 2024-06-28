<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\EventSubscriber;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

trait ProcessedClassesTrait
{
    private array $processedClasses = [];

    private function isProcessed(ClassName $className): bool
    {
        return array_key_exists($className->fqcn(), $this->processedClasses);
    }

    private function markAsProcessed(ClassName $className): void
    {
        $this->processedClasses[$className->fqcn()] = null;
    }
}
