<?php

namespace Dealroadshow\JsonSchema\DataType;

use Dealroadshow\JsonSchema\DataType\Factory\DataTypeFactoryInterface;
use Dealroadshow\JsonSchema\DataType\Factory\TypeAwareFactoryInterface;
use Dealroadshow\JsonSchema\DataType\Factory\TypeUnawareFactoryInterface;

class DataTypesService
{
    /**
     * @var DataTypeFactoryInterface[]|iterable
     */
    private iterable $factories;

    /**
     * @var DataTypeFactoryInterface[]|array<string, DataTypeFactoryInterface>
     */
    private array $typedFactoriesMap;

    /**
     * @param DataTypeFactoryInterface[]|iterable $factories
     */
    public function __construct(iterable $factories)
    {
        $this->factories = $factories;
        $this->buildTypedFactoriesMap();
    }

    public function determineType(array $jsonSchema): DataTypeInterface
    {
        if (\array_key_exists('type', $jsonSchema)) {
            $typeName = $jsonSchema['type'];
            $this->ensureValidTypeName($typeName);
            $factory = $this->typedFactoriesMap[$typeName];

            return $factory->createFromSchema($jsonSchema, $this)->setSchema($jsonSchema);
        }

        foreach ($this->factories as $factory) {
            if (!$factory instanceof TypeUnawareFactoryInterface) {
                continue; // This schema is not typed
            }

            if ($factory->recognizesSchema($jsonSchema)) {
                return $factory->createFromSchema($jsonSchema, $this)->setSchema($jsonSchema);
            }
        }

        return UnknownType::fromJsonSchema($jsonSchema);
    }

    private function ensureValidTypeName(string $typeName)
    {
        if (!\array_key_exists($typeName, $this->typedFactoriesMap)) {
            throw new \InvalidArgumentException(
                \sprintf('Unknown type name "%s"', $typeName)
            );
        }
    }

    private function buildTypedFactoriesMap(): void
    {
        $this->typedFactoriesMap = [];
        foreach ($this->factories as $factory) {
            if (!$factory instanceof TypeAwareFactoryInterface) {
                continue;
            }

            $expectedTypes = $factory->expectedTypes();
            foreach ($expectedTypes as $expectedType) {
                if (\array_key_exists($expectedType, $this->typedFactoriesMap)) {
                    throw new \LogicException(
                        \sprintf(
                            'Factories "%s" and "%s" both expect type "%s"',
                            \get_class($factory),
                            \get_class($this->typedFactoriesMap[$expectedType]),
                            $expectedType
                        )
                    );
                }
                $this->typedFactoriesMap[$expectedType] = $factory;
            }
        }
    }
}
