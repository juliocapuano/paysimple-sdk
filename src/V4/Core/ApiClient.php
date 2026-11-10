<?php

namespace PaySimple\V4\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private Client $client;
    private bool $hasErrors = false;

    public function __construct(Config $config)
    {
        $this->client = new Client($config->generateConfig());
    }

    private function checkForStatusError(int $statusCode, object $content): array
    {
        $this->hasErrors = false;
        $data            = ['errors' => []];

        if (in_array($statusCode, Config::HTTP_ERROR_CODES, true)) {
            $this->hasErrors = true;
            $apiErrors       = $content->Meta->Errors;

            if ($statusCode === 500) {
                $data['errors'][] = sprintf(
                    '%s - code: %s',
                    $apiErrors->ErrorCode,
                    $apiErrors->TraceCode
                );
            } else {
                foreach ($apiErrors->ErrorMessages as $apiError) {
                    $data['errors'][] = $apiError->Message;
                }
            }
        }

        if (!in_array($statusCode, Config::HTTP_SUCCESS_CODES, true)) {
            $this->hasErrors = true;
            $data['errors'][] = 'Unexpected: ' . json_encode($content);
        }

        if ($this->hasErrors()) {
            return [
                'error' => $this->hasErrors(),
                'data'  => $data,
            ];
        }

        return [];
    }

    private function processResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $content    = json_decode($response->getBody()->getContents());

        $error = $this->checkForStatusError($statusCode, $content);
        if ($error) {
            return $error;
        }

        return [
            'error' => false,
            'data'  => $content->Response,
            'meta'  => $content->Meta,
        ];
    }

    final public function post(string $endpoint, array $data = []): array
    {
        $response = $this->client->post($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    final public function get(string $endpoint, array $data = []): array
    {
        $response = $this->client->get($endpoint, ['query' => $data]);
        return $this->processResponse($response);
    }

    final public function put(string $endpoint, array $data = []): array
    {
        $response = $this->client->put($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    final public function delete(string $endpoint, array $data = []): array
    {
        $response = $this->client->delete($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    final public function hasErrors(): bool
    {
        return $this->hasErrors;
    }
}
