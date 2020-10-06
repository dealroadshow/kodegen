<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type;

class PHPType
{
    const BOOL   = 'bool';
    const INT    = 'int';
    const FLOAT  = 'float';
    const STRING = 'string';

    private ?string $name;
    private string $docType;
    private bool $nullable;

    public function __construct(?string $name, string $docType, bool $nullable)
    {
        $this->name = $name;
        $this->docType = $docType;
        $this->nullable = $nullable;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function docType(): string
    {
        return $this->docType;
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }
}
