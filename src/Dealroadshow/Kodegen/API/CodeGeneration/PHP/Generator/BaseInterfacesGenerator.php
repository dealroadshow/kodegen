<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Nette\PhpGenerator\ClassType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

class BaseInterfacesGenerator
{
    private const API_RESOURCE_INTERFACE = 'APIResourceInterface';
    private const API_RESOURCE_LIST_INTERFACE = 'APIResourceListInterface';
    private const OBJECT_META = 'ObjectMeta';
    private const LIST_META = 'ListMeta';

    public function resourceInterface(string $namespacePrefix): PHPClass
    {
        return $this->generate(
            $namespacePrefix,
            self::API_RESOURCE_INTERFACE,
            self::OBJECT_META
        );
    }

    public function resourceListInterface(string $namespacePrefix): PHPClass
    {
        return $this->generate(
            $namespacePrefix,
            self::API_RESOURCE_LIST_INTERFACE,
            self::LIST_META
        );
    }

    private function generate(string $namespacePrefix, string $className, string $metadataClass): PHPClass
    {
        $className = ClassName::fromNamespaceAndName(
            $namespacePrefix,
            $className
        );

        $class = new ClassType($className->shortName());
        $class
            ->setInterface()
            ->addExtend(\JsonSerializable::class);

        $metadataClass = $namespacePrefix.'\\Data\\'.$metadataClass;
        $class
            ->addMethod('metadata')
            ->setReturnType($metadataClass)
            ->setReturnNullable(false);

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\JsonSerializable::class));
        $phpClass->useClass(ClassName::fromFQCN($metadataClass));

        return $phpClass;
    }
}
