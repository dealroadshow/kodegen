<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObject;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

interface VOClassGeneratorInterface
{
    public function generate(ClassName $className, DataTypeInterface $type, Context $context, PHPTypesService $service): PHPClass;
    public function supports(ClassName $className, DataTypeInterface $type): bool;
}
