<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObject\VOClassGeneratorInterface;

class ValueObjectsService
{
    private PHPTypesService $phpTypesService;
    private GeneratedClassesCache $cache;

    /**
     * @var VOClassGeneratorInterface[]|iterable
     */
    private iterable $generators;

    /**
     * @param PHPTypesService                      $phpTypesService
     * @param GeneratedClassesCache                $cache
     * @param VOClassGeneratorInterface[]|iterable $generators
     */
    public function __construct(PHPTypesService $phpTypesService, GeneratedClassesCache $cache, iterable $generators)
    {
        $this->cache = $cache;
        $this->generators = $generators;
        $this->phpTypesService = $phpTypesService;
    }

    public function generateVOClass(string $definitionName, DataTypeInterface $type, Context $context): PHPClass
    {
        $className = $this->createClassName($definitionName, $context);
        if ($this->cache->has($className->fqcn())) {
            return $this->cache->get($className->fqcn());
        }
        foreach ($this->generators as $generator) {
            if ($generator->supports($className, $type)) {
                $phpClass = $generator->generate($className, $type, $context, $this->phpTypesService);
                $this->cache->set($className->fqcn(), $phpClass);

                return $phpClass;
            }
        }

        throw new \LogicException(
            \sprintf(
                'There are no generators able to create VO class for definition "%s" with type "%s"',
                $definitionName,
                $type::class
            )
        );
    }

    private function createClassName(string $definitionName, Context $context): ClassName
    {
        $namespaceName = $context->namespacePrefix.'\\ValueObject';

        return ClassName::fromDefinitionName($namespaceName, $definitionName);
    }
}
