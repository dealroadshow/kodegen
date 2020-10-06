<?php

namespace Dealroadshow\JsonSchema\DataType;

final class StringType extends AbstractType
{
    const FORMAT_RAW = 'raw'; // pseudo type
    const FORMAT_BYTES = 'bytes';
    const FORMAT_DATETIME = 'date-time';
    const FORMAT_EMAIL = 'email';
    const FORMAT_HOSTNAME = 'hostname';
    const FORMAT_IPV4 = 'ipv4';
    const FORMAT_IPV6 = 'ipv6';
    const FORMAT_URI = 'uri';

    public function format(): string
    {
        return $this->schema['format'] ?? self::FORMAT_RAW;
    }
}
