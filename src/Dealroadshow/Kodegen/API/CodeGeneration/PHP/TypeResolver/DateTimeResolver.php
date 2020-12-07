<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\StringType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GeneratedClassesCache;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Nette\PhpGenerator\ClassType;

class DateTimeResolver extends AbstractTypeResolver
{
    private const NAMESPACE_PREFIX = 'Data';
    private const GENERATED_CLASS_NAME = 'DateTimeInterface';

    public function __construct(private GeneratedClassesCache $cache)
    {
    }

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable): PHPType
    {
        $namespaceName = $context->namespacePrefix().'\\'.self::NAMESPACE_PREFIX;
        $className = ClassName::fromNamespaceAndName($namespaceName, self::GENERATED_CLASS_NAME);
        $fqcn = $className->fqcn();
        if (!$this->cache->has($fqcn)) {
            $classType = new ClassType($className->shortName());
            $classType
                ->setInterface()
                ->addExtend(\DateTimeInterface::class)
                ->addExtend(\JsonSerializable::class)
            ;

            $phpClass = new PHPClass($className, $classType);
            $phpClass
                ->useClass(ClassName::fromFQCN(\DateTimeInterface::class))
                ->useClass(ClassName::fromFQCN(\JsonSerializable::class))
            ;

            $this->cache->set($fqcn, $phpClass);
        }

        return new PHPType($fqcn, $fqcn, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME === $type->format();
    }
}
