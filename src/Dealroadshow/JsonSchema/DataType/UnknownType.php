<?php

namespace Dealroadshow\JsonSchema\DataType;

final class UnknownType extends AbstractType
{
    public static function fromJsonSchema(array $schema): UnknownType
    {
        return (new self())->setSchema($schema);
    }
}
