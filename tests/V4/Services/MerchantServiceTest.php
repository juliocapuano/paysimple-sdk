<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\MerchantService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\MerchantPaymentOptions;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use stdClass;

class MerchantServiceTest extends MockeryTestCase
{
    protected $apiClientMock;
    protected $merchantService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->merchantService = new MerchantService($this->apiClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testPaymentOptionsSuccessfully()
    {
        $apiResponseData = new stdClass();
        $apiResponseData->AcceptsCreditCard = true;
        $apiResponseData->AcceptsAch = true;
        $apiResponseData->CreditCardIssuers = 'Visa,Mastercard,Amex,Discover';

        $this->apiClientMock->shouldReceive('get')
            ->with('merchant/paymentoptions')
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->merchantService->paymentOptions();

        $this->assertInstanceOf(MerchantPaymentOptions::class, $result);
        $this->assertSame((bool)$apiResponseData->AcceptsCreditCard, $result->AcceptsCreditCard);
        $this->assertSame((bool)$apiResponseData->AcceptsAch, $result->AcceptsAch);
        $this->assertEquals($apiResponseData->CreditCardIssuers, $result->CreditCardIssuers);
    }

    public function testPaymentOptionsThrowsExceptionOnError()
    {
        $errorMessages = ['Service unavailable'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with('merchant/paymentoptions')
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 503]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Service unavailable');

        $this->merchantService->paymentOptions();
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }
}
