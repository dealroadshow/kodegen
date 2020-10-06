<?php

namespace App\Command;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\CodeGenerationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class GeneratePhpCommand extends Command
{
    protected static $defaultName = 'generate:php';

    private CodeGenerationService $service;
    private array $jsonSchemaVersions;
    private string $jsonSchemaUrlTemplate;

    public function __construct(CodeGenerationService $service, array $jsonSchemaVersions, string $jsonSchemaUrlTemplate)
    {
        $this->service = $service;
        $this->jsonSchemaVersions = $jsonSchemaVersions;
        $this->jsonSchemaUrlTemplate = $jsonSchemaUrlTemplate;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates PHP classes from Kubernetes json schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $k8sVersion = $this->askForK8sVersion($io);
        $schemaVersion = $this->jsonSchemaVersions[$k8sVersion];
        $jsonSchemaUrl = \str_replace('{version}', $schemaVersion, $this->jsonSchemaUrlTemplate);
        $namespacePrefix = $this->askForNamespacePrefix($io);
        $rootDir = $this->askForRootDir($io);


        $this->service->generate($jsonSchemaUrl, $namespacePrefix, $rootDir);

        $io->success(
            \sprintf('PHP code was saved to directory "%s"', $rootDir)
        );
        $io->writeln('');

        return 0;
    }

    private function askForK8sVersion(SymfonyStyle $io): string
    {
        $question = new ChoiceQuestion(
            'Please select your version of Kubernetes',
            \array_keys($this->jsonSchemaVersions)
        );
        $question->setErrorMessage(
            \sprintf('Please select variants from 0 to %d', \count($this->jsonSchemaVersions) - 1)
        );

        return $io->askQuestion($question);
    }

    private function askForNamespacePrefix(SymfonyStyle $io): string
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

    private function askForRootDir(SymfonyStyle $io): string
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
