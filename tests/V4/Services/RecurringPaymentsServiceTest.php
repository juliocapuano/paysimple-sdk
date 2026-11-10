<?php

namespace PaySimple\Tests\V4\Services;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\RecurringPaymentsService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RecurringPaymentsServiceTest extends TestCase
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

    public function testCrudOperations(): void
    {
        $apiClient = $this->createApiClient([
            new Response(201, [], json_encode(['Response' => ['Id' => 301], 'Meta' => ['HttpStatus' => 201]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 301], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['Id' => 301, 'amount' => 120], 'Meta' => ['HttpStatus' => 200]])),
            new Response(204, [], json_encode(['Response' => null, 'Meta' => ['HttpStatus' => 204]])),
        ]);

        $service = new RecurringPaymentsService($apiClient);

        $new = $service->new(['amount' => 100]);
        $this->assertSame(301, $new['data']->Id);

        $get = $service->get(301);
        $this->assertSame(301, $get['data']->Id);

        $update = $service->update(['Id' => 301, 'amount' => 120]);
        $this->assertSame(120, $update['data']->amount);

        $delete = $service->delete(301);
        $this->assertNull($delete['data']);
    }

    public function testListAndFilterOperations(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode(['Response' => [['Id' => 1]], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => [['Id' => 1]], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => [['payment_id' => 701]], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new RecurringPaymentsService($apiClient);

        $this->assertCount(1, $service->list(['status' => 'Active'])['data']);
        $this->assertCount(1, $service->customerList(10)['data']);
        $this->assertCount(1, $service->paymentList(301)['data']);
    }

    public function testScheduleControlOperations(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode(['Response' => ['status' => 'Suspended'], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['status' => 'Paused'], 'Meta' => ['HttpStatus' => 200]])),
            new Response(200, [], json_encode(['Response' => ['status' => 'Active'], 'Meta' => ['HttpStatus' => 200]])),
        ]);

        $service = new RecurringPaymentsService($apiClient);

        $date = new DateTime('2026-12-31');

        $suspend = $service->suspend(301);
        $this->assertSame('Suspended', $suspend['data']->status);

        $pause = $service->pause(301, $date);
        $this->assertSame('Paused', $pause['data']->status);

        $resume = $service->resume(301);
        $this->assertSame('Active', $resume['data']->status);
    }
}
