<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type;

class ClassName
{
    private string $namespace;
    private string $shortName;
    private string $fqcn;

    private function __construct(string $namespace, string $shortName)
    {
        $this->namespace = $namespace;
        $this->shortName = $shortName;
        $this->fqcn = $this->namespace.'\\'.$shortName;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function shortName(): string
    {
        return $this->shortName;
    }

    public function fqcn(): string
    {
        return $this->fqcn;
    }

    public static function isFQCN(string $string): bool
    {
        return \str_contains($string, '\\') && !\str_contains($string, '|');
    }

    public static function fromNamespaceAndName(string $namespace, string $shortName): self
    {
        return new self($namespace, $shortName);
    }

    public static function fromFQCN(string $fqcn): self
    {
        $parts = \explode('\\', $fqcn);
        $shortName = \array_pop($parts);
        $namespace = \implode('\\', $parts);
        $namespace = trim($namespace, '\\');

        return new self($namespace, $shortName);
    }

    public static function fromDefinitionName(string $namespace, string $definitionName): self
    {
        $parts = \explode('.', $definitionName);
        $shortName = \ucfirst(\end($parts));

        return new self($namespace, $shortName);
    }
}
