<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType\Factory;

interface TypeUnawareFactoryInterface extends DataTypeFactoryInterface
{
    /**
     * This method tells whether factory can create corresponding data type using given schema
     *
     * @param array $schema
     *
     * @return bool
     */
    public function recognizesSchema(array $schema): bool;
}
