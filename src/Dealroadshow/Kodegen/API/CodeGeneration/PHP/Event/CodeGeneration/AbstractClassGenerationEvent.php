<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractClassGenerationEvent extends Event implements ClassGenerationEventInterface
{
    protected ClassName $className;
    protected Context $context;
    protected ?PHPClass $class;

    public function __construct(ClassName $className, Context $context)
    {
        $this->className = $className;
        $this->context = $context;
        $this->class = null;
    }

    public function className(): ClassName
    {
        return $this->className;
    }

    public function context(): Context
    {
        return $this->context;
    }

    public function hasClass(): bool
    {
        return null !== $this->class;
    }

    public function getClass(): PHPClass
    {
        return $this->class;
    }

    public function setClass(PHPClass $class): void
    {
        $this->class = $class;
    }
}
