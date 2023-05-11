<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

class DataClassGenerationEvent extends AbstractClassGenerationEvent
{
    private ObjectType $objectType;

    public function __construct(ClassName $className, Context $context, ObjectType $objectType)
    {
        $this->objectType = $objectType;

        parent::__construct($className, $context);
    }

    public function objectType(): ObjectType
    {
        return $this->objectType;
    }
}
