<?php

declare(strict_types=1);

namespace App\Command;

use App\Kubernetes\JsonSchemaVersionsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'k8s:schema:versions',
    description: 'This command retrieves the latest json schema versions for a number of Kubernetes minor versions. This json schema versions then can be used to retrieve the latest json schema for some minor Kubernetes version. For example the latest json schema version for Kubernetes v1.16 is v1.16.4.'
)]
class K8SSchemaVersionsCommand extends Command
{
    private const ARGUMENT_NAME = 'numberOfVersions';
    private const DEFAULT_NUMBER_OF_VERSIONS = 4;

    protected static $defaultName = 'k8s:schema:versions';
    /**
     * @var JsonSchemaVersionsService
     */
    private JsonSchemaVersionsService $service;

    public function __construct(JsonSchemaVersionsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_NAME,
                InputArgument::OPTIONAL,
                'The number of latest Kubernetes versions to retrieve json schema versions for',
                self::DEFAULT_NUMBER_OF_VERSIONS
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $numberOfVersions = $input->getArgument(self::ARGUMENT_NAME);

        $map = $this->service->latestVersionsMap($numberOfVersions);

        $io->writeln(\json_encode($map));

        return 0;
    }
}
