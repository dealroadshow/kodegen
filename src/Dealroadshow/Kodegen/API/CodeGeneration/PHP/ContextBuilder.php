<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\TypesMap;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinitionsMap;

class ContextBuilder
{
    private ?ApiObjectDefinitionsMap $definitions;
    private ?TypesMap $types;
    private ?string $rootDir;
    private ?string $namespacePrefix;
    private ?ClassName $resourceInterface;
    private ?ClassName $resourceListInterface;

    private function __construct()
    {
    }

    public function setDefinitions(ApiObjectDefinitionsMap $definitions): self
    {
        $this->definitions = $definitions;

        return $this;
    }

    public function setTypes(TypesMap $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function setRootDir(string $rootDir): self
    {
        $this->rootDir = $rootDir;

        return $this;
    }

    public function setNamespacePrefix(string $namespacePrefix): self
    {
        $this->namespacePrefix = $namespacePrefix;

        return $this;
    }

    public function setResourceInterface(ClassName $className): self
    {
        $this->resourceInterface = $className;

        return $this;
    }

    public function setResourceListInterface(ClassName $className): self
    {
        $this->resourceListInterface = $className;

        return $this;
    }

    public function build(): Context
    {
        $props = (new \ReflectionObject($this))->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            if (null === $prop->getValue($this)) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Cannot build config without value for "%s" property',
                        $prop->getName()
                    )
                );
            }
        }

        return new Context(
            $this->definitions,
            $this->types,
            $this->rootDir,
            $this->namespacePrefix,
            $this->resourceInterface,
            $this->resourceListInterface
        );
    }

    public static function instance(): self
    {
        return new self();
    }
}