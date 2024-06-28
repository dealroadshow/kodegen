<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use App\Util\ClassUtil;
use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\JsonSchemaService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator\DataClassGenerator;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinitionsMap;

class GenericCodeGenerationService implements CodeGenerationServiceInterface
{
    public function __construct(
        private JsonSchemaService $jsonSchemaService,
        private DataClassGenerator $classGenerator,
        private GeneratedClassesCache $cache
    ) {
    }

    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir, ClassName $resourceInterface = null, ClassName $resourceListInterface = null): void
    {
        $typesMap = $this->jsonSchemaService->typesMap($jsonSchema);
        $context = ContextBuilder::instance()
            ->setDefinitions(ApiObjectDefinitionsMap::fromArray([])) // dummy call, definitions are not used
            ->setTypes($typesMap)
            ->setOutputDir($rootDir)
            ->setNamespacePrefix($namespacePrefix)
            ->setResourceInterface(ClassName::fromFQCN('Dummy')) // not generated
            ->setResourceListInterface(ClassName::fromFQCN('Dummy')) // not generated
            ->build();

        $definitions = $typesMap
            ->filter(fn (DataTypeInterface $type) => $type instanceof ObjectType)
            ->getIterator();

        $this->classGenerator->setNamespacePrefix('');
        foreach ($definitions as $name => $definition) {
            $this->classGenerator->generateFromDefinitionName($name, $definition, $context);
        }

        foreach ($this->cache as $fqcn => $class) {
            /** @var PHPClass $class */
            if ($class->isExternal) {
                continue;
            }
            $path = ClassUtil::filePathForClass($fqcn, $context);
            file_put_contents($path, $class->toString($context));
        }
    }
}
