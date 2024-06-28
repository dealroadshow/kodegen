<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\TypesMap;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinitionsMap;

readonly class Context
{
    public function __construct(
        public ApiObjectDefinitionsMap $definitions,
        public TypesMap $types,
        public string $outputDir,
        public string $namespacePrefix,
        public ClassName $resourceInterface,
        public ClassName $resourceListInterface
    ) {
    }
}
