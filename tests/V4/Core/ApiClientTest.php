<?php

namespace PaySimple\Tests\V4\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ApiClientTest extends TestCase
{
    private function createApiClientWithResponses(array $responses): ApiClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $config = new Config('user', 'token', true);
        $clientConfig = array_merge($config->generateConfig(), ['handler' => $handlerStack]);
        $guzzleClient = new Client($clientConfig);

        $apiClient = new ApiClient($config);

        $reflection = new ReflectionClass($apiClient);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($apiClient, $guzzleClient);

        return $apiClient;
    }

    public function testGetSuccess(): void
    {
        $payload = json_encode([
            'Response' => ['id' => 123, 'name' => 'John'],
            'Meta' => ['HttpStatus' => 200]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(200, [], $payload)
        ]);

        $result = $apiClient->get('test-endpoint', ['foo' => 'bar']);

        $this->assertFalse($result['error']);
        $this->assertFalse($apiClient->hasErrors());
        $this->assertSame(123, $result['data']->id);
        $this->assertSame(200, $result['meta']->HttpStatus);
    }

    public function testPostSuccess(): void
    {
        $payload = json_encode([
            'Response' => ['id' => 456],
            'Meta' => ['HttpStatus' => 201]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(201, [], $payload)
        ]);

        $result = $apiClient->post('test-endpoint', ['name' => 'Test']);

        $this->assertFalse($result['error']);
        $this->assertFalse($apiClient->hasErrors());
        $this->assertSame(456, $result['data']->id);
    }

    public function testPutSuccess(): void
    {
        $payload = json_encode([
            'Response' => ['status' => 'updated'],
            'Meta' => ['HttpStatus' => 200]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(200, [], $payload)
        ]);

        $result = $apiClient->put('test-endpoint', ['status' => 'updated']);

        $this->assertFalse($result['error']);
        $this->assertSame('updated', $result['data']->status);
    }

    public function testDeleteSuccess(): void
    {
        $payload = json_encode([
            'Response' => null,
            'Meta' => ['HttpStatus' => 204]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(204, [], $payload)
        ]);

        $result = $apiClient->delete('test-endpoint');

        $this->assertFalse($result['error']);
        $this->assertNull($result['data']);
    }

    public function testError400Handling(): void
    {
        $payload = json_encode([
            'Meta' => [
                'HttpStatus' => 400,
                'Errors' => [
                    'ErrorMessages' => [
                        ['Message' => 'Field is required'],
                        ['Message' => 'Invalid value']
                    ]
                ]
            ]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(400, [], $payload)
        ]);

        $result = $apiClient->get('test-endpoint');

        $this->assertTrue($result['error']);
        $this->assertTrue($apiClient->hasErrors());
        $this->assertContains('Field is required', $result['data']['errors']);
        $this->assertContains('Invalid value', $result['data']['errors']);
    }

    public function testError500Handling(): void
    {
        $payload = json_encode([
            'Meta' => [
                'HttpStatus' => 500,
                'Errors' => [
                    'ErrorCode' => 'InternalError',
                    'TraceCode' => 'TR12345'
                ]
            ]
        ]);
        $apiClient = $this->createApiClientWithResponses([
            new Response(500, [], $payload)
        ]);

        $result = $apiClient->post('test-endpoint', []);

        $this->assertTrue($result['error']);
        $this->assertTrue($apiClient->hasErrors());
        $this->assertTrue(in_array('InternalError - code: TR12345', $result['data']['errors']));
    }

    public function testUnexpectedStatusCodeHandling(): void
    {
        $payload = json_encode(['foo' => 'bar']);
        $apiClient = $this->createApiClientWithResponses([
            new Response(202, [], $payload)
        ]);

        $result = $apiClient->get('test-endpoint');

        $this->assertTrue($result['error']);
        $this->assertTrue($apiClient->hasErrors());
        $this->assertStringStartsWith('Unexpected:', $result['data']['errors'][0]);
    }
}
