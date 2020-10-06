<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;


use Nette\PhpGenerator\ClassType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GeneratedClassesCache;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;

abstract class AbstractCollectionClassGenerator
{
    protected const NAMESPACE_PREFIX = 'Data\Collection';
    protected const PROPERTY_NAME = 'items';

    private GeneratedClassesCache $cache;

    abstract protected static function classNameSuffix(): string;
    abstract protected static function propertyDocType(PHPType $itemType): string;
    abstract protected function defineAddMethod(ClassType $class, PHPType $itemType): self;

    public function __construct(GeneratedClassesCache $cache)
    {
        $this->cache = $cache;
    }

    public function generate(PHPType $itemType, Context $context): PHPClass
    {
        $className = $this->className($itemType, $context);
        $fqcn = $className->fqcn();
        if ($this->cache->has($fqcn)) {
            return $this->cache->get($fqcn);
        }

        $class = new ClassType($className->shortName());
        $this
            ->generateForClass($class, $itemType)
            ->defineJsonSerializeMethod($class);

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\JsonSerializable::class));

        $this->cache->set($fqcn, $phpClass);

        return $phpClass;
    }

    public function generateForClass(ClassType $class, PHPType $itemType): self
    {
        $this
            ->defineProperty($class, $itemType)
            ->defineAddMethod($class, $itemType)
            ->defineAddAllMethod($class, $itemType)
            ->defineAllMethod($class, $itemType)
            ->defineClearMethod($class)
            ->defineConstructor($class)
            ->defineOtherMethods($class, $itemType)
        ;

        return $this;
    }

    protected function className(PHPType $type, Context $context): ClassName
    {
        $itemTypeName = $type->name();
        if (ClassName::isFQCN($itemTypeName)) {
            $itemClassName = ClassName::fromFQCN($itemTypeName);
            $itemTypeName = $itemClassName->shortName();
        }

        $namespaceName = $context->namespacePrefix().'\\'.self::NAMESPACE_PREFIX;
        $shortClassName = \ucfirst($itemTypeName).static::classNameSuffix();

        return ClassName::fromNamespaceAndName($namespaceName, $shortClassName);
    }

    protected function defineOtherMethods(ClassType $classType, PHPType $itemType): self
    {
        return $this;
    }

    private function defineProperty(ClassType $class, PHPType $itemType): self
    {
        $class
            ->addProperty(self::PROPERTY_NAME)
            ->setPrivate()
            ->setType('array')
            ->setValue([])
            ->setInitialized(true)
            ->setNullable(false)
            ->addComment(PHP_EOL)
            ->addComment(
                sprintf('@var %s', $this->propertyDocType($itemType))
            )
            ->addComment(PHP_EOL);

        return $this;
    }

    private function defineAddAllMethod(ClassType $class, PHPType $itemType): self
    {
        $method = $class
            ->addMethod('addAll')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $param = $method
            ->addParameter('items')
            ->setType('array')
            ->setNullable(false);

        $paramDocType = $this->propertyDocType($itemType);
        $method
            ->addComment(PHP_EOL)
            ->addComment('@var '.$paramDocType.' $'.$param->getName())
            ->addComment('')
            ->addComment('@return self')
            ->addComment(PHP_EOL);

        $method
            ->addBody(
                \sprintf(
                    '$this->%s = array_merge($this->%s, $%s);',
                    self::PROPERTY_NAME,
                    self::PROPERTY_NAME,
                    $param->getName()
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    private function defineAllMethod(ClassType $class, PHPType $itemType): self
    {
        $method = $class
            ->addMethod('all')
            ->setReturnType('array')
            ->setReturnNullable(false);

        $method
            ->addComment(PHP_EOL)
            ->addComment('@return '.$this->propertyDocType($itemType))
            ->addComment(PHP_EOL);

        $method->addBody(
            \sprintf('return $this->%s;', self::PROPERTY_NAME)
        );

        return $this;
    }

    private function defineClearMethod(ClassType $class): self
    {
        $method = $class
            ->addMethod('clear')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $method
            ->addBody(
                \sprintf('$this->%s = [];', self::PROPERTY_NAME)
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    private function defineConstructor(ClassType $class): self
    {
        $class
            ->addMethod('__construct')
            ->addBody('$this->clear();');

        return $this;
    }

    private function defineJsonSerializeMethod(ClassType $class)
    {
        $class
            ->addMethod('jsonSerialize')
            ->addBody(
                \sprintf('return $this->%s;', self::PROPERTY_NAME)
            );

        $class->addImplement(\JsonSerializable::class);
    }
}