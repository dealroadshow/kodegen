<?php

namespace Dealroadshow\JsonSchema\DataType\Factory;

use Dealroadshow\JsonSchema\DataType\DataTypesService;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;

class ObjectTypeFactory implements TypeAwareFactoryInterface
{
    private const EXPECTED_TYPE = 'object';

    public function createFromSchema(array $schema, DataTypesService $service): ObjectType
    {
        $requiredProperties = $schema['required'] ?? [];
        $properties = [];
        foreach ($schema['properties'] ?? [] as $name => $propertySchema) {
            $required = \in_array($name, $requiredProperties);
            $type = $service->determineType($propertySchema);
            $property = new PropertyDefinition($name, $type, $required);
            $properties[$name] = $property;

            $description = $propertySchema['description'] ?? null;
            if (null !== $description) {
                $description = \wordwrap($description, 80);
                $property->setDescription($description);
            }
        }

        $additionalPropsType = \array_key_exists('additionalProperties', $schema)
            ? $service->determineType($schema['additionalProperties'])
            : null;

        return new ObjectType($properties, $additionalPropsType);
    }

    public function expectedTypes(): array
    {
        return [ self::EXPECTED_TYPE ];
    }
}
