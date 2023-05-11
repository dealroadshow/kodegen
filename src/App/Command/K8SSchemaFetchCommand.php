<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class K8SSchemaFetchCommand extends Command
{
    private const ARGUMENT_FILE_PATH = 'filePath';

    protected static $defaultName = 'k8s:schema:fetch';

    public function __construct(
        private HttpClientInterface $httpClient,
        private array $jsonSchemaVersions,
        private string $jsonSchemaUrlTemplate
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Fetches Kubernetes json schema and saves to file')
            ->addArgument(
                self::ARGUMENT_FILE_PATH,
                InputArgument::REQUIRED,
                'Path where to save fetched json schema'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $k8sVersion = $this->askForK8sVersion($io);
        $schemaVersion = $this->jsonSchemaVersions[$k8sVersion];
        $jsonSchemaUrl = \str_replace('{version}', $schemaVersion, $this->jsonSchemaUrlTemplate);

        $filePath = $input->getArgument(self::ARGUMENT_FILE_PATH);
        $json = $this->fetchJson($jsonSchemaUrl);
        file_put_contents($filePath, $json);

        $io->success(sprintf('Json schema saved to file "%s"', $filePath));
        $io->writeln('');

        return self::SUCCESS;
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

    private function fetchJson(string $jsonSchemaUrl): string
    {
        try {
            $response = $this->httpClient->request('GET', $jsonSchemaUrl);

            return $response->getContent(true);
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException(
                \sprintf(
                    "Cannot fetch json schema from URL '%s':\n%s with message:\n%s",
                    $jsonSchemaUrl,
                    $e::class,
                    $e->getMessage()
                )
            );
        }
    }
}
