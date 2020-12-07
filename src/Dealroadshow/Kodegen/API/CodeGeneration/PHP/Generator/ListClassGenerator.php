<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Nette\PhpGenerator\ClassType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class ListClassGenerator extends AbstractCollectionClassGenerator
{
    private const CLASS_NAME_SUFFIX = 'List';

    protected function defineAddMethod(ClassType $class, PHPType $itemType): AbstractCollectionClassGenerator
    {
        $method = $class
            ->addMethod('add')
            ->setReturnType('self')
            ->setReturnNullable(false);
        $valueParam = $method
            ->addParameter('value')
            ->setType($itemType->name())
            ->setNullable(false);

        $method
            ->addBody(
                \sprintf(
                    '$this->%s[] = $%s;',
                    self::PROPERTY_NAME,
                    $valueParam->getName()
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected static function classNameSuffix(): string
    {
        return self::CLASS_NAME_SUFFIX;
    }
}
