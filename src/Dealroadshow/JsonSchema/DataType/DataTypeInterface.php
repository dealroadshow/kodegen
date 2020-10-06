<?php

namespace Dealroadshow\JsonSchema\DataType;

interface DataTypeInterface
{
    const TYPE_ARRAY = 'array';
    const TYPE_BOOL = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_OBJECT = 'object';
    const TYPE_STRING = 'string';
    const TYPE_REFERENCE = 'reference';

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
