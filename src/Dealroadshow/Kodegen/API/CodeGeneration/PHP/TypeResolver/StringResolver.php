<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\StringType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class StringResolver extends ScalarResolver
{
    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME !== $type->format();
    }

    protected function phpType(DataTypeInterface $type): string
    {
        return PHPType::STRING;
    }
}
