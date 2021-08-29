<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use App\Util\ClassUtil;
use Dealroadshow\JsonSchema\JsonSchemaService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\APIClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\BaseInterfacesGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\Definitions\Objects\ObjectDefinitionsService;

class K8SCodeGenerationService implements CodeGenerationServiceInterface
{
    private ObjectDefinitionsService $definitionsService;
    private JsonSchemaService $jsonSchemaService;
    private APIClassGenerator $classGenerator;
    private BaseInterfacesGenerator $interfacesGenerator;
    private GeneratedClassesCache $cache;

    public function __construct(ObjectDefinitionsService $definitionsService, JsonSchemaService $jsonSchemaService, APIClassGenerator $classGenerator, BaseInterfacesGenerator $interfacesGenerator, GeneratedClassesCache $cache)
    {
        $this->definitionsService = $definitionsService;
        $this->jsonSchemaService = $jsonSchemaService;
        $this->classGenerator = $classGenerator;
        $this->interfacesGenerator = $interfacesGenerator;
        $this->cache = $cache;
    }

    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir): void
    {
        $typesMap = $this->jsonSchemaService->typesMap($jsonSchema);
        $definitionsMap = $this->definitionsService->topLevelDefinitionsMap($typesMap);
        $resourceInterface = $this->resourceInterface($namespacePrefix);
        $resourceListInterface = $this->resourceListInterface($namespacePrefix);
        $context = ContextBuilder::instance()
            ->setDefinitions($definitionsMap)
            ->setTypes($typesMap)
            ->setRootDir($rootDir)
            ->setNamespacePrefix($namespacePrefix)
            ->setResourceInterface($resourceInterface->name())
            ->setResourceListInterface($resourceListInterface->name())
            ->build();

        foreach ($context->definitions() as $definition) {
            $this->classGenerator->generate($definition, $context);
        }

        foreach ($this->cache as $fqcn => $class) {
            $path = ClassUtil::filePathForClass($fqcn, $context);
            file_put_contents($path, $class->toString($context));
        }
    }

    private function resourceInterface(string $namespacePrefix): PHPClass
    {
        $resourceInterface = $this->interfacesGenerator->resourceInterface(
            $namespacePrefix
        );
        $this->cache->set($resourceInterface->name()->fqcn(), $resourceInterface);

        return $resourceInterface;
    }

    private function resourceListInterface(string $namespacePrefix): PHPClass
    {
        $resourceListInterface = $this->interfacesGenerator->resourceListInterface(
            $namespacePrefix
        );
        $this->cache->set($resourceListInterface->name()->fqcn(), $resourceListInterface);

        return $resourceListInterface;
    }
}
