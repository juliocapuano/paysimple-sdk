<?php

namespace PaySimple\Tests\V4\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\PaymentService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PaymentServiceTest extends TestCase
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

    public function testPaymentOperations(): void
    {
        $apiClient = $this->createApiClient([
            new Response(201, [], json_encode(['Response' => ['Id' => 501], 'Meta' => ['HttpStatus' => 201]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 501, 'Status' => 'Authorized'], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 501, 'Status' => 'Refunded'], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new PaymentService($apiClient);

        $new = $service->new(['AccountId' => 10, 'Amount' => 50.00]);
        $this->assertSame(501, $new['data']->Id);

        $get = $service->get(501);
        $this->assertSame('Authorized', $get['data']->Status);

        $refund = $service->refund(501);
        $this->assertSame('Refunded', $refund['data']->Status);
    }

    public function testVoidAndList(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode(['Response' => ['Id' => 501, 'Status' => 'Voided'], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => [['Id' => 501]], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new PaymentService($apiClient);

        $void = $service->void(501);
        $this->assertSame('Voided', $void['data']->Status);

        $list = $service->list(['page' => 1]);
        $this->assertCount(1, $list['data']);
    }
}
