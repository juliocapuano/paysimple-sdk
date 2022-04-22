<?php


namespace PaySimple\V4\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiClient
 * @package PaySimple\V4\Core
 */
class ApiClient
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var bool
     */
    private $has_errors = false;

    public function __construct(Config $config)
    {
        $this->client = new Client($config->generateConfig());
    }

    /**
     * @param int $status_code
     * @param Object $content
     * @return array
     */
    private function checkForStatusError(int $status_code, object $content): array
    {
        $this->has_errors = false;
        $data             = ['errors' => []];

        if (in_array($status_code, Config::HTTP_ERRORS_CODES)) { // si son códigos de error
            $this->has_errors = true;
            $api_errors       = $content->Meta->Errors;

            if ($status_code === 500) {
                $data['errors'][] = sprintf("%s - code: %s", $api_errors->ErrorCode, $api_errors->TraceCode);
            } else {
                foreach ($api_errors->ErrorMessages as $api_error) {
                    $data['errors'][] = $api_error->Message;
                }
            }
        }

        if (!in_array($status_code, Config::HTTP_SUCCESS_CODES)) { //si no son códigos de error pero tampoco de OK
            $this->has_errors = true;
            $data['errors'][] = 'Unexpected: ' . json_encode($content);
        }

        if ($this->hasErrors()) {
            $error = $this->hasErrors();
            return compact('error', 'data');
        }

        return [];
    }


    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function processResponse(ResponseInterface $response): array
    {
        $status_code = $response->getStatusCode();
        $content     = json_decode($response->getBody()->getContents());

        $error = $this->checkForStatusError($status_code, $content);
        if ($error) {
            return $error;
        }

        $error = false;
        $data  = $content->Response;
        $meta  = $content->Meta;

        return compact('error', 'data', 'meta');
    }


    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    final public function post(string $endpoint, array $data = []): array
    {
        $response = $this->client->post($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    final public function get(string $endpoint, array $data = []): array
    {
        $response = $this->client->get($endpoint, ['query' => $data]);
        return $this->processResponse($response);
    }


    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    final public function put(string $endpoint, array $data = []): array
    {
        $response = $this->client->put($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    final public function delete(string $endpoint, array $data = []): array
    {
        $response = $this->client->delete($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }

    /**
     * @return bool
     */
    final public function hasErrors(): bool
    {
        return $this->has_errors;
    }
}
