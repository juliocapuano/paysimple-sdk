<?php

namespace PaySimple\Tests\V4\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\AccountService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AccountServiceTest extends TestCase
{
    private function createApiClient(array $responses, ?array &$historyContainer = null): ApiClient
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

    public function testCreditCardMethods(): void
    {
        $apiClient = $this->createApiClient([
            new Response(201, [], json_encode(['Response' => ['id' => 1], 'Meta' => ['HttpStatus' => 201]])),
            new Response(200, [], json_encode(['Response' => ['id' => 10], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['id' => 10], 'Meta' => ['HttpStatus' => 200]])),
            new Response(204, [], json_encode(['Response' => null, 'Meta' => ['HttpStatus' => 204]])),
        ]);

        $service = new AccountService($apiClient);

        $newCard = $service->newCreditCard(['card' => 'data']);
        $this->assertSame(1, $newCard['data']->id);

        $getCard = $service->getCreditCard(10);
        $this->assertSame(10, $getCard['data']->id);

        $updateCard = $service->updateCreditCard(['id' => 10, 'exp' => '12/26']);
        $this->assertSame(10, $updateCard['data']->id);

        $deleteCard = $service->deleteCreditCard(10);
        $this->assertNull($deleteCard['data']);
    }

    public function testAchMethods(): void
    {
        $apiClient = $this->createApiClient([
            new Response(201, [], json_encode(['Response' => ['id' => 2], 'Meta' => ['HttpStatus' => 201]])),
            new Response(200, [], json_encode(['Response' => ['id' => 20], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['id' => 20], 'Meta' => ['HttpStatus' => 200]])),
            new Response(204, [], json_encode(['Response' => null, 'Meta' => ['HttpStatus' => 204]])),
        ]);

        $service = new AccountService($apiClient);

        $newAch = $service->newAch(['routing' => '123456789']);
        $this->assertSame(2, $newAch['data']->id);

        $getAch = $service->getAch(20);
        $this->assertSame(20, $getAch['data']->id);

        $updateAch = $service->updateAch(['id' => 20]);
        $this->assertSame(20, $updateAch['data']->id);

        $deleteAch = $service->deleteAch(20);
        $this->assertNull($deleteAch['data']);
    }
}
