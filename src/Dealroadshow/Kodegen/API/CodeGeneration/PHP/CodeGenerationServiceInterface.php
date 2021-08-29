<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

interface CodeGenerationServiceInterface
{
    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir): void;
}
