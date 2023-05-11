<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType\Factory;

interface TypeAwareFactoryInterface extends DataTypeFactoryInterface
{
    /**
     * This method tells, which values of "type" field in json schema it expects.
     * It's used in order to speed up corresponding factory determination for any given typed schema
     *
     * @return string[]|array
     */
    public function expectedTypes(): array;
}
