<?php

namespace PaySimple\Tests\V4\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\MerchantService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MerchantServiceTest extends TestCase
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

    public function testPaymentOptions(): void
    {
        $apiClient = $this->createApiClient([
            new Response(200, [], json_encode([
                'Response' => [
                    'CreditCardTypes' => ['Visa', 'MasterCard'],
                    'AchTypes' => ['Checking', 'Savings']
                ],
                'Meta' => ['HttpStatus' => 200]
            ]))
        ]);

        $service = new MerchantService($apiClient);
        $result = $service->paymentOptions();

        $this->assertFalse($result['error']);
        $this->assertContains('Visa', $result['data']->CreditCardTypes);
    }
}
