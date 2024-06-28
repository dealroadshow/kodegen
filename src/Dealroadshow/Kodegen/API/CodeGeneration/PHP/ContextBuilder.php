<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\TypesMap;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinitionsMap;

class ContextBuilder
{
    private ApiObjectDefinitionsMap|null $definitions = null;
    private TypesMap|null $types = null;
    private string|null $outputDir = null;
    private string|null $namespacePrefix = null;
    private ClassName|null $resourceInterface = null;
    private ClassName|null $resourceListInterface = null;

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

    public function setOutputDir(string $outputDir): self
    {
        $this->outputDir = $outputDir;

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
            $this->outputDir,
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
