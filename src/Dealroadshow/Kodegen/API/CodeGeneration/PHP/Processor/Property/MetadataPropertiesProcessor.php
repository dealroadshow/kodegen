<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;

class MetadataPropertiesProcessor extends AbstractPropertyDefinitionProcessor
{
    private const DESCRIPTION_PATTERN_TO_MATCH = '/populated\s+by\s+(the\s+)?system/i';

    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        $property->skip();
    }

    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool
    {
        return 'ObjectMeta' === $className->shortName()
            && null !== $property->description()
            && 0 !== preg_match(self::DESCRIPTION_PATTERN_TO_MATCH, $property->description());
    }
}
