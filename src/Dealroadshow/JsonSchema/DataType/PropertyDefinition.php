<?php

namespace Dealroadshow\JsonSchema\DataType;

class PropertyDefinition
{
    private string $name;
    private ?string $description = null;
    private DataTypeInterface $type;
    private bool $required;
    private bool $nullable;
    private bool $skipped;

    /**
     * Closure that will be called during generation of class constructor.
     * This closure should initialize this property in constructor, for example
     * it may initialize \ArrayObject property by adding line like:
     *
     * '$this->[propertyName] = new \ArrayObject();'
     *
     * Closure will receive three parameters:
     * \Nette\Property instance, \Nette\Method instance (constructor) and \Nette\ClassType instance
     *
     * It can than use this arguments to generate some code in constructor
     */
    private ?\Closure $initializer = null;

    public function __construct(string $name, DataTypeInterface $type, bool $required)
    {
        $this->name = \ltrim($name, '$');
        $this->type = $type;
        $this->required = $required;
        $this->nullable = !$required;
        $this->skipped = false;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function hasInitializer(): bool
    {
        return null !== $this->initializer;
    }

    /**
     * See property comment
     *
     * @return \Closure
     */
    public function getInitializer(): \Closure
    {
        return $this->initializer;
    }

    /**
     * See property comment
     *
     * @param \Closure $initializer
     */
    public function setInitializer(\Closure $initializer): void
    {
        $this->initializer = $initializer;
    }

    public function type(): DataTypeInterface
    {
        return $this->type;
    }

    public function required(): bool
    {
        return $this->required;
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }

    public function skipped(): bool
    {
        return $this->skipped;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;
        if ($required) {
            $this->nullable = false;
        }

        return $this;
    }

    public function skip(): self
    {
        $this->skipped = true;

        return $this;
    }

    public function setNullable(bool $nullable): self
    {
        if ($this->required && $nullable) {
            throw new \LogicException(
                \sprintf('Required property "%s" cannot be nullable', $this->name)
            );
        }

        $this->nullable = $nullable;

        return $this;
    }
}
