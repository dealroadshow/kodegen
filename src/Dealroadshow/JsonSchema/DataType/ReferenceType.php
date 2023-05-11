<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType;

final class ReferenceType extends AbstractType
{
    private string $referencedDefinitionName;

    public function __construct(string $referencedDefinitionName)
    {
        $this->referencedDefinitionName = $referencedDefinitionName;
    }

    public function referencedDefinitionName(): string
    {
        return $this->referencedDefinitionName;
    }
}
