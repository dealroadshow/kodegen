<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

class MapClassGenerator extends AbstractCollectionClassGenerator
{
    private const CLASS_NAME_SUFFIX = 'Map';

    protected function defineAddMethod(ClassType $class, PHPType $itemType): static
    {
        $method = $class
            ->addMethod('add')
            ->setReturnType('self')
            ->setReturnNullable(false);
        $nameParam = $this->defineNameParam($method);
        $valueParam = $method
            ->addParameter('value')
            ->setType($itemType->name())
            ->setNullable(false);

        $method
            ->addBody(
                \sprintf(
                    '$this->%s[$%s] = $%s;',
                    self::PROPERTY_NAME,
                    $nameParam->getName(),
                    $valueParam->getName()
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected function defineOtherMethods(ClassType $classType, PHPType $itemType): static
    {
        $this
            ->defineHasMethod($classType)
            ->defineGetMethod($classType, $itemType)
            ->defineRemoveMethod($classType);

        return $this;
    }

    private function defineHasMethod(ClassType $class): self
    {
        $method = $class
            ->addMethod('has')
            ->setReturnType('bool')
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);
        $method->addBody(
            \sprintf(
                'return array_key_exists($%s, $this->%s);',
                $param->getName(),
                self::PROPERTY_NAME
            )
        );

        return $this;
    }

    private function defineGetMethod(ClassType $class, PHPType $itemType): self
    {
        $method = $class
            ->addMethod('get')
            ->setReturnType($itemType->name())
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);

        $method->addBody(
            \sprintf(
                'return $this->%s[$%s];',
                self::PROPERTY_NAME,
                $param->getName()
            )
        );

        return $this;
    }

    private function defineRemoveMethod(ClassType $class): self
    {
        $method = $class
            ->addMethod('remove')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);
        $method
            ->addBody(
                \sprintf('unset($this->%s[$%s]);', self::PROPERTY_NAME, $param->getName())
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    private function defineNameParam(Method $method): Parameter
    {
        return $method
            ->addParameter('name')
            ->setType('string')
            ->setNullable(false);
    }

    protected static function classNameSuffix(): string
    {
        return self::CLASS_NAME_SUFFIX;
    }
}
