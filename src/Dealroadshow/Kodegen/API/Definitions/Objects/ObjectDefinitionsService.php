<?php

namespace Dealroadshow\Kodegen\API\Definitions\Objects;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\TypesMap;
use Dealroadshow\SemVer\VersionsService;

class ObjectDefinitionsService
{
    private VersionsService $versionsService;

    public function __construct(VersionsService $versionsService)
    {
        $this->versionsService = $versionsService;
    }

    public function topLevelDefinitionsMap(TypesMap $typesMap): ApiObjectDefinitionsMap
    {
        $objectsMap = ApiObjectDefinitionsMap::fromTypesMap(
            $this->onlyObjects($typesMap)
        );

        return $this->withoutDuplicatesByKind($objectsMap);
    }

    private function onlyObjects(TypesMap $typesMap): TypesMap
    {
        return $typesMap->filter(function(DataTypeInterface $type) {
            return
                $type instanceof ObjectType
                && $type->hasAnnotation(ApiObjectDefinition::ANNOTATION_GROUP_VERSION_KIND)
                && $type->hasProperty(ApiObjectDefinition::PROPERTY_METADATA);
        });
    }

    private function withoutDuplicatesByKind(ApiObjectDefinitionsMap $definitionsMap): ApiObjectDefinitionsMap
    {
        $groupedByKind = [];
        foreach ($definitionsMap as $name => $definition) {
            $kind = $definition->kind();
            $version = $definition->version()->string();
            $groupedByKind[$kind][$version] ??= $definition;
        }

        $newMap = [];
        foreach ($groupedByKind as $kind => $definitionsOfKind) {
            $versions = \array_map(
                fn(ApiObjectDefinition $definition) => $definition->version(),
                $definitionsOfKind
            );

            $latestStable = $this->versionsService->latestMostStable($versions);
            $chosenDefinition = $definitionsOfKind[$latestStable->string()];
            $newMap[$chosenDefinition->name()] = $chosenDefinition;
        }

        return ApiObjectDefinitionsMap::fromArray($newMap);
    }
}
