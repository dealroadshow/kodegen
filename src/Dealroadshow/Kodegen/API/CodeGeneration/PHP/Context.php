<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\TypesMap;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinitionsMap;

class Context
{
    private ApiObjectDefinitionsMap $definitions;
    private TypesMap $types;
    private string $rootDir;
    private string $namespacePrefix;
    private ClassName $resourceInterface;
    private ClassName $resourceListInterface;

    public function __construct(ApiObjectDefinitionsMap $definitions, TypesMap $types, string $rootDir, string $namespacePrefix, ClassName $resourceInterface, ClassName $resourceListInterface)
    {
        $this->definitions = $definitions;
        $this->types = $types;
        $this->rootDir = $rootDir;
        $this->namespacePrefix = $namespacePrefix;
        $this->resourceInterface = $resourceInterface;
        $this->resourceListInterface = $resourceListInterface;
    }

    public function resourceInterface(): ClassName
    {
        return $this->resourceInterface;
    }

    public function resourceListInterface(): ClassName
    {
        return $this->resourceListInterface;
    }

    public function definitions(): ApiObjectDefinitionsMap
    {
        return $this->definitions;
    }

    public function types(): TypesMap
    {
        return $this->types;
    }

    public function rootDir(): string
    {
        return $this->rootDir;
    }

    public function namespacePrefix(): string
    {
        return $this->namespacePrefix;
    }
}
