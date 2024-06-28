<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

interface CodeGenerationServiceInterface
{
    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir, ClassName $resourceInterface = null, ClassName $resourceListInterface = null): void;
}
