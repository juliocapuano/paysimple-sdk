<?php

namespace PaySimple\Tests\V4\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\CustomerService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CustomerServiceTest extends TestCase
{
    private function createApiClient(array $responses): ApiClient
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

    public function testCustomerCrud(): void
    {
        $apiClient = $this->createApiClient([
            new Response(201, [], json_encode(['Response' => ['Id' => 1], 'Meta' => ['HttpStatus' => 201]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 1], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 1], 'Meta' => ['HttpStatus' => 200]])),
            new Response(204, [], json_encode(['Response' => null, 'Meta' => ['HttpStatus' => 204]])),
        ]);

        $service = new CustomerService($apiClient);

        $new = $service->new(['FirstName' => 'John']);
        $this->assertSame(1, $new['data']->Id);

        $get = $service->get(1);
        $this->assertSame(1, $get['data']->Id);

        $update = $service->update(['Id' => 1, 'FirstName' => 'Johnny']);
        $this->assertSame(1, $update['data']->Id);

        $delete = $service->delete(1);
        $this->assertNull($delete['data']);
    }

    public function testCustomerListsAndAccounts(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode(['Response' => [['Id' => 1]], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['accounts' => []], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['ach' => []], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['cards' => []], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['default_ach' => 5], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['default_card' => 6], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new CustomerService($apiClient);

        $this->assertCount(1, $service->list(['page' => 1])['data']);
        $this->assertSame([], $service->listAccounts(1)['data']->accounts);
        $this->assertSame([], $service->achAccounts(1)['data']->ach);
        $this->assertSame([], $service->creditCardsAccounts(1)['data']->cards);
        $this->assertSame(5, $service->defaultAch(1)['data']->default_ach);
        $this->assertSame(6, $service->defaultCreditCard(1)['data']->default_card);
    }

    public function testCustomerAccountAssignmentAndPaymentList(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode(['Response' => ['success' => true], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => [], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new CustomerService($apiClient);

        $this->assertTrue($service->setDefaultAccount(1, 99)['data']->success);
        $this->assertSame([], $service->listOfPayments(1, ['limit' => 10])['data']);
    }
}
