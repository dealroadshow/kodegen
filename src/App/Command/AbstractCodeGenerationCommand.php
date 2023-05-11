<?php

declare(strict_types=1);

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\CodeGenerationServiceInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCodeGenerationCommand extends Command
{
    private const ARGUMENT_SCHEMA_PATH = 'json-schema-path';
    private const OPTION_NAMESPACE_PREFIX = 'namespace-prefix';
    private const OPTION_OUTPUT_DIR = 'output-dir';
    private const OPTION_RESOURCE_INTERFACE = 'resource-interface';
    private const OPTION_RESOURCE_LIST_INTERFACE = 'resource-list-interface';

    public function __construct(protected CodeGenerationServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates PHP classes from Kubernetes json schema')
            ->addArgument(
                self::ARGUMENT_SCHEMA_PATH,
                InputArgument::REQUIRED,
                'Path to a file that contains valid json schema definitions'
            )
            ->addOption(
                name: self::OPTION_NAMESPACE_PREFIX,
                mode: InputOption::VALUE_REQUIRED,
                description: 'PHP namespace prefix, that all generated classes will have. For example "My\\Org\\Name"'
            )
            ->addOption(
                name: self::OPTION_OUTPUT_DIR,
                mode: InputOption::VALUE_REQUIRED,
                description: 'Path to directory, where generated classes will be saved'
            )
            ->addOption(
                name: self::OPTION_RESOURCE_INTERFACE,
                mode: InputOption::VALUE_REQUIRED,
                description: 'PHP interface FQCN, that all generated API classes will implement. If specified, interface itself is not generated. If empty, will be deducted from namespace prefix'
            )
            ->addOption(
                name: self::OPTION_RESOURCE_LIST_INTERFACE,
                mode: InputOption::VALUE_REQUIRED,
                description: 'PHP interface FQCN, that all generated API class lists will implement. If specified, interface itself is not generated. If empty, will be deducted from namespace prefix'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $namespacePrefix = $this->getNamespacePrefix($input, $io);
        $outputDir = $this->getOutputDir($input, $io);
        $resourceInterface = $input->getOption(self::OPTION_RESOURCE_INTERFACE);
        $resourceInterface = $resourceInterface ? ClassName::fromFQCN($resourceInterface): null;
        $resourceListInterface = $input->getOption(self::OPTION_RESOURCE_LIST_INTERFACE);
        $resourceListInterface = $resourceListInterface ? ClassName::fromFQCN($resourceListInterface): null;

        $jsonSchemaPath = $input->getArgument(self::ARGUMENT_SCHEMA_PATH);
        if (!file_exists($jsonSchemaPath)) {
            $io->error(sprintf('Json schema path "%s" does not exist.', $jsonSchemaPath));
            $io->writeln('');

            return self::FAILURE;
        }

        $json = \file_get_contents($jsonSchemaPath);
        $schema = \json_decode($json, true);
        if (null === $schema && JSON_ERROR_NONE !== json_last_error()) {
            $io->error(sprintf('File "%s" does not contain valid json', $jsonSchemaPath));
            $io->writeln('');

            return self::FAILURE;
        }

        $this->service->generate($schema, $namespacePrefix, $outputDir, $resourceInterface, $resourceListInterface);

        $io->success(
            \sprintf('PHP code was saved to directory "%s"', $outputDir)
        );
        $io->writeln('');

        return self::SUCCESS;
    }

    private function getNamespacePrefix(InputInterface $input, SymfonyStyle $io): string
    {
        $validator = function ($value) {
            if (!\is_string($value) || 0 === \strlen($value)) {
                throw new \RuntimeException('Namespace prefix cannot be empty');
            }

            return $value;
        };

        $namespacePrefix = $input->getOption(self::OPTION_NAMESPACE_PREFIX);
        if ($namespacePrefix) {
            return $validator($namespacePrefix);
        }

        $question = new Question('Please specify namespace prefix for generated classes');
        $question->setValidator($validator);

        return $io->askQuestion($question);
    }

    private function getOutputDir(InputInterface $input, SymfonyStyle $io): string
    {
        $validator = function ($value) {
            $homeDir = \getenv('HOME');
            if ($homeDir) {
                $value = \str_replace('~', $homeDir, $value);
            }
            $path = \realpath($value);

            if (!\file_exists($path)) {
                throw new \RuntimeException(
                    \sprintf('Path "%s" does not exist', $path)
                );
            }

            if (!\is_dir($path)) {
                throw new \RuntimeException(
                    \sprintf('Path "%s" must be a directory, but file is found', $path)
                );
            }

            return $path;
        };

        $outputDir = $input->getOption(self::OPTION_OUTPUT_DIR);
        if ($outputDir) {
            return $outputDir;
        }

        $question = new Question('Please specify directory where to save PHP classes');
        $question->setValidator();

        return $io->askQuestion($question);
    }
}
