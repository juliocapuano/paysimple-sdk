<?php


namespace PaySimple\V4;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Config $config)
    {
        $this->client = new Client($config->generateConfig());
    }

    /**
     * @param int $status_code
     * @param Object $content
     * @return array|bool
     */
    private function checkForStatusError(int $status_code, Object $content)
    {
        $error = false;
        $data  = ['errors' => []];

        if (in_array($status_code, Config::HTTP_ERRORS_CODES)) { // si son códigos de error
            $error      = true;
            $api_errors = $content->Meta->Errors;

            if ($status_code === 500) {
                $data['errors'][] = "{$api_errors->ErrorCode} - code: {$api_errors->TraceCode}";
            } else {
                foreach ($api_errors->ErrorMessages as $idx => $api_error) {
                    $data['errors'][] = $api_error->Message;
                }
            }
        }

        if (!in_array($status_code, Config::HTTP_SUCCESS_CODES)) { //si no son códigos de error pero tampoco de OK
            $error            = true;
            $data['errors'][] = 'Unexpected: ' . json_encode($content);
        }

        if ($error) {
            return compact('error', 'data');
        }

        return false;
    }


    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function processResponse(ResponseInterface $response)
    {
        $status_code = $response->getStatusCode();
        $content     = json_decode($response->getBody()->getContents());

        if ($error = $this->checkForStatusError($status_code, $content)) {
            return $error;
        }

        $error = false;
        $data  = $content->Response;
        $meta  = $content->Meta;

        return compact('error', 'data', 'meta');
    }


    /**
     * @param string $endpoint
     * @param null|array|mixed $data
     * @return array
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $data = [])
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
    public function get(string $endpoint, array $data = [])
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
    public function put(string $endpoint, array $data = [])
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
    public function delete(string $endpoint, array $data = [])
    {
        $response = $this->client->delete($endpoint, ['json' => $data]);
        return $this->processResponse($response);
    }
}
