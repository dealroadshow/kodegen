<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

interface ClassGenerationEventInterface
{
    public function className(): ClassName;
    public function context(): Context;
    public function hasClass(): bool;
    public function getClass(): PHPClass;
    public function setClass(PHPClass $class): void;
}
