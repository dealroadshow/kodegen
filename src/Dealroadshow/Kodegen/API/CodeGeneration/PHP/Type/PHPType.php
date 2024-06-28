<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type;

readonly class PHPType
{
    public const BOOL   = 'bool';
    public const INT    = 'int';
    public const FLOAT  = 'float';
    public const STRING = 'string';

    public function __construct(public string|null $name, public string $docType, public bool $nullable)
    {
    }
}
