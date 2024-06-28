<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\Definitions\Objects;

use Dealroadshow\JsonSchema\DataType\ObjectType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\SemVer\Version;

class ApiObjectDefinition
{
    public const ANNOTATION_GROUP_VERSION_KIND = 'x-kubernetes-group-version-kind';
    public const PROPERTY_METADATA = 'metadata';

    private string $name;
    private ObjectType $type;
    private string $group;
    private Version $version;
    private string $kind;

    private function __construct(string $name, ObjectType $type)
    {
        $this->name = $name;
        $this->type = $type;

        $gvk = $type->getAnnotation(self::ANNOTATION_GROUP_VERSION_KIND)[0];
        $this->group = $gvk['group'];
        $this->version = Version::fromString($gvk['version']);
        $this->kind = $gvk['kind'];
    }

    public function apiVersion(): string
    {
        return $this->group
            ? $this->group.'/'.$this->version->string()
            : $this->version->string();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): ObjectType
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->type->description();
    }

    public function group(): string
    {
        return $this->group;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function kind(): string
    {
        return $this->kind;
    }

    /**
     * @return PropertyDefinition[]|iterable
     */
    public function properties(): iterable
    {
        return $this->type->properties();
    }

    public static function fromObjectType(string $name, ObjectType $type): self
    {
        return new self($name, $type);
    }
}
