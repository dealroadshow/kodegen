<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinition;

class APIClassGenerationEvent extends AbstractClassGenerationEvent
{
    private ApiObjectDefinition $definition;

    public function __construct(ClassName $className, Context $context, ApiObjectDefinition $definition)
    {
        $this->definition = $definition;

        parent::__construct($className, $context);
    }

    public function definition(): ApiObjectDefinition
    {
        return $this->definition;
    }
}
