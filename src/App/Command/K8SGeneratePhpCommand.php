<?php

declare(strict_types=1);

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\K8SCodeGenerationService;

class K8SGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    protected static $defaultName = 'k8s:generate:php';

    public function __construct(K8SCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
