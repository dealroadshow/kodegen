<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\JsonSchemaService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\APIClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\BaseInterfacesGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\Definitions\Objects\ObjectDefinitionsService;

class CodeGenerationService
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

    public function generate(string $jsonSchemaUrl, string $namespacePrefix, string $rootDir)
    {
        $typesMap = $this->jsonSchemaService->typesMap($jsonSchemaUrl);
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
            $path = $this->filePathForClass($fqcn, $context);
            file_put_contents($path, $class->toString($context));
        }
    }

    private function filePathForClass(string $fqcn, Context $context): string
    {
        $pattern = sprintf('/^%s/', \preg_quote($context->namespacePrefix()));
        $withoutNamespacePrefix = \preg_replace($pattern, '', $fqcn);
        $relativePath = \str_replace('\\', DIRECTORY_SEPARATOR, $withoutNamespacePrefix);
        $absPath = $context->rootDir().DIRECTORY_SEPARATOR.$relativePath.'.php';
        $absPath = \str_replace('//', '/', $absPath);

        $dir = \dirname($absPath);
        if (!\file_exists($dir)) {
            \mkdir(\dirname($absPath), 0777, true);
        }

        return $absPath;
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
