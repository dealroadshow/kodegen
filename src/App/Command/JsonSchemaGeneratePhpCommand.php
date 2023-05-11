<?php

declare(strict_types=1);

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GenericCodeGenerationService;

class JsonSchemaGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    protected static $defaultName = 'json-schema:generate:php';

    public function __construct(GenericCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
