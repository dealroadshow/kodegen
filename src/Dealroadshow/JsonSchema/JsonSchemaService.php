<?php

namespace Dealroadshow\JsonSchema;

use Dealroadshow\JsonSchema\DataType\DataTypesService;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JsonSchemaService
{
    private HttpClientInterface $httpClient;
    private DataTypesService $typesService;

    public function __construct(HttpClientInterface $httpClient, DataTypesService $typesService)
    {
        $this->httpClient = $httpClient;
        $this->typesService = $typesService;
    }

    /**
     * @param string $jsonSchemaUrl
     *
     * @return array
     */
    public function fetch(string $jsonSchemaUrl): array
    {
        try {
            $response = $this->httpClient->request('GET', $jsonSchemaUrl);

            return \json_decode($response->getContent(true), true);
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException(
                \sprintf(
                    "Cannot fetch json schema from URL '%s':\n%s with message:\n%s",
                    $jsonSchemaUrl,
                    \get_class($e),
                    $e->getMessage()
                )
            );
        }
    }

    public function typesMap(string $jsonSchemaUrl): TypesMap
    {
        $schema = $this->fetch($jsonSchemaUrl);

        $map = [];
        foreach ($schema['definitions'] as $name => $definition) {
            $map[$name] = $this->typesService->determineType($definition);
        }

        return new TypesMap($map);
    }
}
