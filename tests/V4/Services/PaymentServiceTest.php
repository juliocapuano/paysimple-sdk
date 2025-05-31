<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\Payment;
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
        $paymentInput = new Payment();
        $paymentInput->AccountId = 123;
        $paymentInput->Amount = 10.00;
        // Populate other necessary fields for Payment::toArray for a 'new' request
        // e.g., $paymentInput->PaymentSubType = 'PPD'; for ACH

        $expectedApiRequestArray = $paymentInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 1;
        $apiResponseData->Status = 'Posted';
        $apiResponseData->Amount = 10.00;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('post')
            ->with('payment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->new($paymentInput);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
        $this->assertEquals($apiResponseData->Amount, $returnedPayment->Amount);
    }

    public function testNewPaymentThrowsExceptionOnError()
    {
        $paymentInput = new Payment();
        $paymentInput->AccountId = 999;
        $paymentInput->Amount = 5.00;
        // Populate other necessary fields

        $expectedApiRequestArray = $paymentInput->toArray();

        $errorMessages = ['Invalid AccountId'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('payment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid AccountId');

        $this->paymentService->new($paymentInput);
    }

    public function testGetPaymentSuccessfully()
    {
        $paymentId = 123;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $paymentId;
        $apiResponseData->Status = 'Settled';
        $apiResponseData->Amount = 20.00;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('get')
            ->with("payment/{$paymentId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->get($paymentId);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
        $this->assertEquals($apiResponseData->Amount, $returnedPayment->Amount);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testListPaymentsSuccessfully()
    {
        $filterParams = ['startdate' => '2024-01-01'];

        $payment1StdClass = new stdClass();
        $payment1StdClass->Id = 1;
        $payment1StdClass->Status = 'Settled';
        $payment1StdClass->Amount = 50.00;

        $payment2StdClass = new stdClass();
        $payment2StdClass->Id = 2;
        $payment2StdClass->Status = 'Posted';
        $payment2StdClass->Amount = 75.00;

        $apiResponseDataArray = [$payment1StdClass, $payment2StdClass];

        $this->apiClientMock->shouldReceive('get')
            ->with('payment', $filterParams)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseDataArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayments = $this->paymentService->list($filterParams);

        $this->assertIsArray($returnedPayments);
        $this->assertCount(2, $returnedPayments);

        foreach ($returnedPayments as $index => $paymentEntity) {
            $this->assertInstanceOf(Payment::class, $paymentEntity);
            $this->assertEquals($apiResponseDataArray[$index]->Id, $paymentEntity->Id);
            $this->assertEquals($apiResponseDataArray[$index]->Status, $paymentEntity->Status);
            $this->assertEquals($apiResponseDataArray[$index]->Amount, $paymentEntity->Amount);
        }
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testRefundPaymentSuccessfully()
    {
        $paymentId = 789;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $paymentId;
        $apiResponseData->Status = 'Reversed';
        $apiResponseData->ReferenceId = 790;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/reverse", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->refund($paymentId);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
        $this->assertEquals($apiResponseData->ReferenceId, $returnedPayment->ReferenceId);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testVoidPaymentSuccessfully()
    {
        $paymentId = 456;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $paymentId;
        $apiResponseData->Status = 'Voided';
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/void", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->void($paymentId);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }
}
