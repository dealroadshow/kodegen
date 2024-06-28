<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType\Factory;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\DataTypesService;

interface DataTypeFactoryInterface
{
    /**
     * This method creates data type, corresponding to this concrete factory
     *
     * @param array            $schema
     * @param DataTypesService $service
     *
     * @return DataTypeInterface
     */
    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface;
}
