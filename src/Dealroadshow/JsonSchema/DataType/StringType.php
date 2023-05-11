<?php

declare(strict_types=1);

namespace Dealroadshow\JsonSchema\DataType;

final class StringType extends AbstractType
{
    public const FORMAT_RAW = 'raw'; // pseudo type
    public const FORMAT_BYTES = 'bytes';
    public const FORMAT_DATETIME = 'date-time';
    public const FORMAT_EMAIL = 'email';
    public const FORMAT_HOSTNAME = 'hostname';
    public const FORMAT_IPV4 = 'ipv4';
    public const FORMAT_IPV6 = 'ipv6';
    public const FORMAT_URI = 'uri';

    public function format(): string
    {
        return $this->schema['format'] ?? self::FORMAT_RAW;
    }
}
