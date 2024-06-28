<?php

declare(strict_types=1);

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\GenericCodeGenerationService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'json-schema:generate:php',
    description: 'Generates PHP classes from json schema',
    aliases: ['json:gen:php']
)]
class JsonSchemaGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    public function __construct(GenericCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
