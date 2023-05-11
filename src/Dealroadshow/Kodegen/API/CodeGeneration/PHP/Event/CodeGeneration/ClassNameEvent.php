<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Symfony\Contracts\EventDispatcher\Event;

class ClassNameEvent extends Event
{
    private ClassName $className;

    public function __construct(ClassName $className)
    {
        $this->className = $className;
    }

    public function getClassName(): ClassName
    {
        return $this->className;
    }

    public function setClassName(ClassName $className): void
    {
        $this->className = $className;
    }
}
