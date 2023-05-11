<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType;

final class UnknownType extends AbstractType
{
    public static function fromJsonSchema(array $schema): UnknownType
    {
        return (new self())->setSchema($schema);
    }
}
