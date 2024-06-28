<?php

declare(strict_types=1);

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\K8SCodeGenerationService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'k8s:generate:php',
    description: 'Generates PHP classes from Kubernetes json schema',
    aliases: ['k8s:gen:php']
)]
class K8SGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    public function __construct(K8SCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
