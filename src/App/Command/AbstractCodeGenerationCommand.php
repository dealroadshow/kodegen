<?php

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\CodeGenerationServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCodeGenerationCommand extends Command
{
    private const ARGUMENT_SCHEMA_PATH = 'jsonSchemaPath';

    public function __construct(protected CodeGenerationServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates PHP classes from Kubernetes json schema')
            ->addArgument(
                self::ARGUMENT_SCHEMA_PATH,
                InputArgument::REQUIRED,
                'Path to file containing valid json schema'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $namespacePrefix = $this->getNamespacePrefix($io);
        $rootDir = $this->getRootDir($io);

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

        $this->service->generate($schema, $namespacePrefix, $rootDir);

        $io->success(
            \sprintf('PHP code was saved to directory "%s"', $rootDir)
        );
        $io->writeln('');

        return self::SUCCESS;
    }

    private function getNamespacePrefix(SymfonyStyle $io): string
    {
        $question = new Question('Please specify namespace prefix for generated classes');
        $question->setValidator(function ($answer) {
            if (!\is_string($answer) || 0 === \strlen($answer)) {
                throw new \RuntimeException('Namespace prefix cannot be empty');
            }

            return $answer;
        });

        return $io->askQuestion($question);
    }

    private function getRootDir(SymfonyStyle $io): string
    {
        $question = new Question('Please specify directory where to save PHP classes');
        $question->setValidator(function ($answer) {
            $homeDir = \getenv('HOME');
            if ($homeDir) {
                $answer = \str_replace('~', $homeDir, $answer);
            }
            $path = \realpath($answer);

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
        });

        return $io->askQuestion($question);
    }
}
