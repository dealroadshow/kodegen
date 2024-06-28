<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

abstract class AbstractPHPClassEvent implements PHPClassEventInterface
{
    private PHPClass $class;

    public function __construct(PHPClass $class)
    {
        $this->class = $class;
    }

    public function getClass(): PHPClass
    {
        return $this->class;
    }
}
