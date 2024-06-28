<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType;

interface DataTypeInterface
{
    public const TYPE_ARRAY = 'array';
    public const TYPE_BOOL = 'boolean';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_NUMBER = 'number';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';
    public const TYPE_REFERENCE = 'reference';

    public function description(): string;

    public function setSchema(array $schema): DataTypeInterface;

    public function schema(): array;

    public function hasAnnotation(string $name): bool;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getAnnotation(string $name);
}
