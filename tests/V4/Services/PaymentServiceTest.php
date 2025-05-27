<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Core\PaySimpleException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use stdClass;

class PaymentServiceTest extends MockeryTestCase
{
    protected $apiClientMock;
    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->paymentService = new PaymentService($this->apiClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testNewPaymentSuccessfully()
    {
        $inputData = ['AccountId' => 123, 'Amount' => 10.00];
        $expectedResponseObject = (object)['Id' => 1, 'Status' => 'Posted', 'Amount' => 10.00];

        $this->apiClientMock->shouldReceive('post')
            ->with('payment', $inputData)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->paymentService->new($inputData);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testNewPaymentThrowsExceptionOnError()
    {
        $inputData = ['AccountId' => 999, 'Amount' => 5.00];
        $errorMessages = ['Invalid AccountId'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('payment', $inputData)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid AccountId');

        $this->paymentService->new($inputData);
    }

    public function testGetPaymentSuccessfully()
    {
        $paymentId = 123;
        $expectedResponseObject = (object)['Id' => $paymentId, 'Status' => 'Settled', 'Amount' => 20.00];

        $this->apiClientMock->shouldReceive('get')
            ->with("payment/{$paymentId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->paymentService->get($paymentId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testGetPaymentThrowsExceptionOnError()
    {
        $paymentId = 456;
        $errorMessages = ['Payment not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with("payment/{$paymentId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Payment not found');

        $this->paymentService->get($paymentId);
    }

    public function testListPaymentsSuccessfully()
    {
        $filterParams = ['startdate' => '2024-01-01'];
        $expectedResponseArray = [
            (object)['Id' => 1, 'Status' => 'Settled'],
            (object)['Id' => 2, 'Status' => 'Posted']
        ];

        $this->apiClientMock->shouldReceive('get')
            ->with('payment', $filterParams)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->paymentService->list($filterParams);
        $this->assertEquals($expectedResponseArray, $result);
    }

    public function testListPaymentsThrowsExceptionOnError()
    {
        $filterParams = ['invalid_param' => 'test'];
        $errorMessages = ['Invalid filter'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with('payment', $filterParams)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid filter');

        $this->paymentService->list($filterParams);
    }

    public function testRefundPaymentSuccessfully()
    {
        $paymentId = 789;
        $expectedResponseObject = (object)['Id' => $paymentId, 'Status' => 'Reversed', 'ReferenceId' => 790];

        // The refund method in SDK sends no body, so we expect null or an empty array.
        // Based on ApiClient::put, it sends ['json' => $data], so if $data is empty, it's ['json'=>[]]
        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/reverse", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->paymentService->refund($paymentId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testRefundPaymentThrowsExceptionOnError()
    {
        $paymentId = 101;
        $errorMessages = ['Payment not settled'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/reverse", [])
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Payment not settled');

        $this->paymentService->refund($paymentId);
    }

    public function testVoidPaymentSuccessfully()
    {
        $paymentId = 456;
        $expectedResponseObject = (object)['Id' => $paymentId, 'Status' => 'Voided'];

        // The void method in SDK sends no body, so we expect null or an empty array.
        // Based on ApiClient::put, it sends ['json' => $data], so if $data is empty, it's ['json'=>[]]
        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/void", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->paymentService->void($paymentId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testVoidPaymentThrowsExceptionOnError()
    {
        $paymentId = 789;
        $errorMessages = ['Payment already settled'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/void", [])
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Payment already settled');

        $this->paymentService->void($paymentId);
    }
}
