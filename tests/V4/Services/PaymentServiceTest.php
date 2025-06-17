<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\Payment;
use PaySimple\V4\Entities\PaymentFailureData;
use PaySimple\V4\Entities\ReceiptOptions;
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
        $paymentInput->PaymentSubType = 'PPD'; // Example for ACH

        $successReceiptOptionsInput = new ReceiptOptions();
        $successReceiptOptionsInput->SendToCustomer = true;
        $successReceiptOptionsInput->SendToOtherAddresses = ['test@example.com'];
        $paymentInput->SuccessReceiptOptions = $successReceiptOptionsInput;

        $expectedApiRequestArray = $paymentInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 1;
        $apiResponseData->Status = 'Posted';
        $apiResponseData->Amount = 10.00;
        $apiResponseData->PaymentSubType = 'PPD';
        // API response for a 'new' payment typically doesn't include FailureData unless it failed synchronously.
        // API response also doesn't typically include ReceiptOptions.
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
        $this->assertEquals($apiResponseData->PaymentSubType, $returnedPayment->PaymentSubType);
        $this->assertNull($returnedPayment->FailureData); // Expect no failure data on success
        // ReceiptOptions are not part of the response for 'new', so they should be null on $returnedPayment
        $this->assertNull($returnedPayment->SuccessReceiptOptions);
        $this->assertNull($returnedPayment->FailureReceiptOptions);
    }

    public function testNewPaymentThrowsExceptionOnError()
    {
        $paymentInput = new Payment();
        $paymentInput->AccountId = 999;
        $paymentInput->Amount = 5.00;
        // If testing serialization of ReceiptOptions even in an error case:
        $failureReceiptOptionsInput = new ReceiptOptions();
        $failureReceiptOptionsInput->SendToCustomer = false;
        $paymentInput->FailureReceiptOptions = $failureReceiptOptionsInput;


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
        $apiResponseData->FailureData = null; // Explicitly null for success case
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
        $this->assertNull($returnedPayment->FailureData);
    }

    public function testGetPaymentSuccessfullyWithFailureData()
    {
        $paymentId = 124;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $paymentId;
        $apiResponseData->Status = 'Failed';
        $apiResponseData->Amount = 25.00;
        $apiResponseData->FailureData = (object)[
            'Code' => 'DF001',
            'Description' => 'Insufficient Funds',
            'MerchantActionText' => 'Contact customer for new payment method.',
            'IsDecline' => true
        ];
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('get')
            ->with("payment/{$paymentId}")
            ->once()
            ->andReturn([
                'error' => false, // API call itself is successful, but payment failed
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->get($paymentId);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
        $this->assertInstanceOf(PaymentFailureData::class, $returnedPayment->FailureData);
        $this->assertEquals($apiResponseData->FailureData->Code, $returnedPayment->FailureData->Code);
        $this->assertEquals($apiResponseData->FailureData->Description, $returnedPayment->FailureData->Description);
        $this->assertTrue($returnedPayment->FailureData->IsDecline);
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
        $payment1StdClass->FailureData = null;

        $payment2StdClass = new stdClass();
        $payment2StdClass->Id = 2;
        $payment2StdClass->Status = 'Failed';
        $payment2StdClass->Amount = 75.00;
        $payment2StdClass->FailureData = (object)[
            'Code' => 'DF002',
            'Description' => 'Card Declined',
            'IsDecline' => true
        ];

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

        // Asserting Payment 1 (Success)
        $this->assertInstanceOf(Payment::class, $returnedPayments[0]);
        $this->assertEquals($payment1StdClass->Id, $returnedPayments[0]->Id);
        $this->assertEquals($payment1StdClass->Status, $returnedPayments[0]->Status);
        $this->assertNull($returnedPayments[0]->FailureData);

        // Asserting Payment 2 (Failed)
        $this->assertInstanceOf(Payment::class, $returnedPayments[1]);
        $this->assertEquals($payment2StdClass->Id, $returnedPayments[1]->Id);
        $this->assertEquals($payment2StdClass->Status, $returnedPayments[1]->Status);
        $this->assertInstanceOf(PaymentFailureData::class, $returnedPayments[1]->FailureData);
        $this->assertEquals($payment2StdClass->FailureData->Code, $returnedPayments[1]->FailureData->Code);
        $this->assertEquals($payment2StdClass->FailureData->Description, $returnedPayments[1]->FailureData->Description);
        $this->assertTrue($returnedPayments[1]->FailureData->IsDecline);
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
        $apiResponseData->Id = $paymentId; // This is actually the ID of the *new* refund payment record
        $apiResponseData->Status = 'Settled'; // Refunds are usually 'Settled' or 'Posted'
        $apiResponseData->ReferenceId = 790; // Original Payment ID that was refunded
        $apiResponseData->IsDebit = true; // Important for refunds
        $apiResponseData->Amount = 10.00; // Amount of the refund
        $apiResponseData->FailureData = null;
        // ... other properties returned by API for a refund payment

        $this->apiClientMock->shouldReceive('put')
            ->with("payment/{$paymentId}/reverse", []) // Here $paymentId is the ID of the payment to be refunded
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData, // This is the new Payment record for the refund
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayment = $this->paymentService->refund($paymentId);
        $this->assertInstanceOf(Payment::class, $returnedPayment);
        $this->assertEquals($apiResponseData->Id, $returnedPayment->Id);
        $this->assertEquals($apiResponseData->Status, $returnedPayment->Status);
        $this->assertEquals($apiResponseData->ReferenceId, $returnedPayment->ReferenceId);
        $this->assertTrue($returnedPayment->IsDebit);
        $this->assertEquals($apiResponseData->Amount, $returnedPayment->Amount);
        $this->assertNull($returnedPayment->FailureData);
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
        $apiResponseData->FailureData = null; // Voided payments shouldn't have new failure data
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
        $this->assertNull($returnedPayment->FailureData);
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
