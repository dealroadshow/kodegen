<?php

namespace Dealroadshow\JsonSchema\DataType;

abstract class AbstractType implements DataTypeInterface
{
    protected array $schema;

    public function description(): string
    {
        return $this->schema['description'] ?? '';
    }

    public function hasAnnotation(string $name): bool
    {
        return \array_key_exists($name, $this->schema);
    }

    public function getAnnotation(string $name)
    {
        return $this->schema[$name];
    }

    /**
     * @return string[]|array
     */
    public function requiredProperties(): array
    {
        return $this->schema['required'] ?? [];
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function setSchema(array $schema): AbstractType
    {
        $this->schema = $schema;

        return $this;
    }
}
