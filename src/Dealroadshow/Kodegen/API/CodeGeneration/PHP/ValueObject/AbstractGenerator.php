<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObject;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Nette\PhpGenerator\ClassType;

abstract class AbstractGenerator implements VOClassGeneratorInterface
{
    protected const VALUE_PROPERTY_NAME = 'value';

    protected function defineProperty(ClassType $class, PHPType $phpType): self
    {
        $property = $class->addProperty(self::VALUE_PROPERTY_NAME);
        $property
            ->setPrivate()
            ->setType($phpType->name)
            ->setNullable(false)
            ->addComment(PHP_EOL)
            ->addComment('@var '.$phpType->docType)
            ->addComment(PHP_EOL);

        return $this;
    }

    protected function defineConstructor(ClassType $class, PHPType $phpType): self
    {
        $constructor = $class->addMethod('__construct');
        $constructor->setPrivate();
        $param = $constructor->addParameter(self::VALUE_PROPERTY_NAME);
        $param
            ->setType($phpType->name)
            ->setNullable(false);
        $constructor->addComment(
            \sprintf('@param %s $%s', $phpType->docType, $param->getName())
        );
        $constructor->addBody(
            \sprintf('$this->%s = $%s;', $param->getName(), $param->getName())
        );

        return $this;
    }

    protected function defineFactoryMethod(ClassType $class, PHPType $phpType, ?string $methodName = null, ?string $paramName = null): self
    {
        $methodName ??= 'from'.\ucfirst($phpType->name);
        $factoryMethod = $class->addMethod($methodName);
        $factoryMethod
            ->setPublic()
            ->setStatic()
            ->setReturnType($class->getName())
            ->setReturnNullable(false);
        $paramName ??= \lcfirst($phpType->name);
        $param = $factoryMethod->addParameter($paramName);
        $param
            ->setType($phpType->name)
            ->setNullable(false);
        $factoryMethod->addBody(
            \sprintf('return new self($%s);', $param->getName())
        );

        return $this;
    }

    protected function defineJsonSerializeMethod(PHPClass $class): self
    {
        $method = $class->classType()->addMethod('jsonSerialize');
        $method->addBody(
            $this->jsonSerializeBody()
        );
        $class->classType()->addImplement(\JsonSerializable::class);
        $class->useClass(ClassName::fromFQCN(\JsonSerializable::class));

        return $this;
    }

    protected function jsonSerializeBody(): string
    {
        return \sprintf('return $this->%s;', self::VALUE_PROPERTY_NAME);
    }
}
