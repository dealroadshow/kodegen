<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;

interface PHPClassEventInterface
{
    public function getClass(): PHPClass;
}
