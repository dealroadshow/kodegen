<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema;

use Dealroadshow\JsonSchema\DataType\DataTypesService;

class JsonSchemaService
{
    private DataTypesService $typesService;

    public function __construct(DataTypesService $typesService)
    {
        $this->typesService = $typesService;
    }

    public function typesMap(array $jsonSchema): TypesMap
    {
        $map = [];
        foreach ($jsonSchema['definitions'] as $name => $definition) {
            $map[$name] = $this->typesService->determineType($definition);
        }

        return new TypesMap($map);
    }
}
