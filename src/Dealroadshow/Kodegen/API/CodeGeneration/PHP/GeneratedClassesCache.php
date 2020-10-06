<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

class GeneratedClassesCache implements \IteratorAggregate
{
    /**
     * @var PHPClass[]|\ArrayObject|array<string, PHPClass>
     */
    private \ArrayObject $map;

    public function __construct()
    {
        $this->map = new \ArrayObject([]);
    }

    public function has(string $fcqn): bool
    {
        return $this->map->offsetExists($fcqn);
    }

    public function get(string $fcqn): PHPClass
    {
        return $this->map[$fcqn];
    }

    public function set(string $fqcn, PHPClass $class): void
    {
        $this->map[$fqcn] = $class;
    }

    public function getIterator()
    {
        return $this->map;
    }
}
