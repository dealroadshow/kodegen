<?php

namespace Dealroadshow\JsonSchema\DataType;

final class ObjectType extends AbstractType
{
    protected array $properties;
    private ?DataTypeInterface $additionalPropertiesType;

    /**
     * @param DataTypeInterface[]|array<string, DataTypeInterface> $properties
     * @param DataTypeInterface|null                               $additionalPropertiesType
     */
    public function __construct(array $properties, ?DataTypeInterface $additionalPropertiesType)
    {
        $this->properties = $properties;
        $this->additionalPropertiesType = $additionalPropertiesType;
    }

    /**
     * @return PropertyDefinition[]
     */
    public function properties(): iterable
    {
        return $this->properties;
    }

    /**
     * @return DataTypeInterface|null
     */
    public function additionalPropertiesType(): ?DataTypeInterface
    {
        return $this->additionalPropertiesType;
    }

    public function allowsAdditionalProperties(): bool
    {
        return null !== $this->additionalPropertiesType;
    }

    public function hasProperty(string $name): bool
    {
        return \array_key_exists($name, $this->properties);
    }

    public function getPropertyType(string $name): DataTypeInterface
    {
        return $this->properties[$name];
    }
}
