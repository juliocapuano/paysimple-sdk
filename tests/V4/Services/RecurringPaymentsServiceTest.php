<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\RecurringPaymentsService;
use PaySimple\V4\Core\PaySimpleException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use stdClass;
use DateTime;

class RecurringPaymentsServiceTest extends MockeryTestCase
{
    protected $apiClientMock;
    protected $recurringPaymentsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->recurringPaymentsService = new RecurringPaymentsService($this->apiClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testNewRecurringPaymentSuccessfully()
    {
        $inputData = [
            'AccountId' => 123,
            'PaymentAmount' => 50.00,
            'StartDate' => '2024-12-01',
            'ExecutionFrequencyType' => 'Monthly'
        ];
        $expectedResponseObject = (object)[
            'Id' => 1,
            'ScheduleStatus' => 'Active',
            'PaymentAmount' => 50.00
        ];

        $this->apiClientMock->shouldReceive('post')
            ->with('recurringpayment', $inputData)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->new($inputData);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testNewRecurringPaymentThrowsExceptionOnError()
    {
        $inputData = [
            'AccountId' => 123,
            'PaymentAmount' => 50.00,
            'StartDate' => 'invalid-date',
            'ExecutionFrequencyType' => 'Monthly'
        ];
        $errorMessages = ['Invalid StartDate'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('recurringpayment', $inputData)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid StartDate');

        $this->recurringPaymentsService->new($inputData);
    }

    public function testGetRecurringPaymentSuccessfully()
    {
        $scheduleId = 456;
        $expectedResponseObject = (object)['Id' => $scheduleId, 'ScheduleStatus' => 'Active'];

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->get($scheduleId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testGetRecurringPaymentThrowsExceptionOnError()
    {
        $scheduleId = 789;
        $errorMessages = ['Schedule not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Schedule not found');

        $this->recurringPaymentsService->get($scheduleId);
    }

    public function testListRecurringPaymentsSuccessfully()
    {
        $filters = ['status' => 'Active'];
        $expectedResponseArray = [
            (object)['Id' => 1, 'ScheduleStatus' => 'Active'],
            (object)['Id' => 2, 'ScheduleStatus' => 'Active']
        ];

        $this->apiClientMock->shouldReceive('get')
            ->with('recurringpayment', $filters)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->list($filters);
        $this->assertEquals($expectedResponseArray, $result);
    }

    public function testListRecurringPaymentsThrowsExceptionOnError()
    {
        $filters = ['invalid_filter' => 'test'];
        $errorMessages = ['Invalid filter'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with('recurringpayment', $filters)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid filter');

        $this->recurringPaymentsService->list($filters);
    }

    public function testCustomerListRecurringPaymentsSuccessfully()
    {
        $customerId = 123;
        $expectedResponseArray = [
            (object)['Id' => 1, 'CustomerId' => $customerId, 'ScheduleStatus' => 'Active'],
            (object)['Id' => 2, 'CustomerId' => $customerId, 'ScheduleStatus' => 'Paused']
        ];

        $this->apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}/recurringpayment")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->customerList($customerId);
        $this->assertEquals($expectedResponseArray, $result);
    }

    public function testCustomerListRecurringPaymentsThrowsExceptionOnError()
    {
        $customerId = 999;
        $errorMessages = ['Customer not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}/recurringpayment")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Customer not found');

        $this->recurringPaymentsService->customerList($customerId);
    }

    public function testPaymentListForScheduleSuccessfully()
    {
        $scheduleId = 789;
        $expectedResponseArray = [
            (object)['Id' => 101, 'Amount' => 50.00],
            (object)['Id' => 102, 'Amount' => 50.00]
        ];

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}/payments")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->paymentList($scheduleId);
        $this->assertEquals($expectedResponseArray, $result);
    }

    public function testPaymentListForScheduleThrowsExceptionOnError()
    {
        $scheduleId = 101;
        $errorMessages = ['Schedule not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}/payments")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Schedule not found');

        $this->recurringPaymentsService->paymentList($scheduleId);
    }

    public function testUpdateRecurringPaymentSuccessfully()
    {
        // As per API docs, all original fields for creation must be included for an update.
        $inputData = [
            'Id' => 123, // Schedule ID to update
            'AccountId' => 456,
            'PaymentAmount' => 55.00, // Updated amount
            'StartDate' => '2024-12-01',
            'EndDate' => '2025-12-01',
            'ExecutionFrequencyType' => 'Monthly',
            'PaymentType' => 'ACH' // Example, add other required fields
        ];
        $expectedResponseObject = (object)[
            'Id' => 123,
            'PaymentAmount' => 55.00,
            'ScheduleStatus' => 'Active'
        ];

        $this->apiClientMock->shouldReceive('put')
            ->with('recurringpayment', $inputData)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->update($inputData);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testUpdateRecurringPaymentThrowsExceptionOnError()
    {
        $inputData = [
            'Id' => 123,
            'PaymentAmount' => -10.00, // Invalid amount
            // Include other required fields as per API for an update
            'AccountId' => 456,
            'StartDate' => '2024-12-01',
            'EndDate' => '2025-12-01',
            'ExecutionFrequencyType' => 'Monthly',
            'PaymentType' => 'ACH'
        ];
        $errorMessages = ['Invalid PaymentAmount'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with('recurringpayment', $inputData)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid PaymentAmount');

        $this->recurringPaymentsService->update($inputData);
    }

    public function testSuspendScheduleSuccessfully()
    {
        $scheduleId = 123;
        $expectedResponseObject = (object)['Id' => $scheduleId, 'ScheduleStatus' => 'Suspended'];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/suspend", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->suspend($scheduleId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testSuspendScheduleThrowsExceptionOnError()
    {
        $scheduleId = 456;
        $errorMessages = ['Schedule already suspended'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/suspend", [])
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Schedule already suspended');

        $this->recurringPaymentsService->suspend($scheduleId);
    }

    public function testPauseScheduleSuccessfully()
    {
        $scheduleId = 123;
        $endDate = new DateTime('2024-12-31');
        $formattedEndDate = $endDate->format('Y-m-d');
        $expectedResponseObject = (object)[
            'Id' => $scheduleId,
            'ScheduleStatus' => 'Suspended', // API docs say "Paused" but example shows "Suspended"
            'PauseUntilDate' => $formattedEndDate
        ];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/pause?enddate={$formattedEndDate}", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->pause($scheduleId, $endDate);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testPauseScheduleThrowsExceptionOnError()
    {
        $scheduleId = 456;
        $endDate = new DateTime('2023-01-01'); // Past date, potentially invalid
        $formattedEndDate = $endDate->format('Y-m-d');
        $errorMessages = ['Invalid end date'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/pause?enddate={$formattedEndDate}", [])
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid end date');

        $this->recurringPaymentsService->pause($scheduleId, $endDate);
    }

    public function testResumeScheduleSuccessfully()
    {
        $scheduleId = 123;
        $expectedResponseObject = (object)[
            'Id' => $scheduleId,
            'ScheduleStatus' => 'Active',
            'PauseUntilDate' => null
        ];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/resume", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->resume($scheduleId);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testResumeScheduleThrowsExceptionOnError()
    {
        $scheduleId = 456;
        $errorMessages = ['Schedule not suspended'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/resume", [])
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Schedule not suspended');

        $this->recurringPaymentsService->resume($scheduleId);
    }

    public function testDeleteRecurringPaymentSuccessfully()
    {
        $scheduleId = 789;

        $this->apiClientMock->shouldReceive('delete')
            ->with("recurringpayment/{$scheduleId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => null, // No content expected for successful deletion
                'meta' => (object)['HttpStatus' => 204]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->recurringPaymentsService->delete($scheduleId);
        $this->assertTrue($result);
    }

    public function testDeleteRecurringPaymentThrowsExceptionOnError()
    {
        $scheduleId = 101;
        $errorMessages = ['Schedule has payments'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('delete')
            ->with("recurringpayment/{$scheduleId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Schedule has payments');

        $this->recurringPaymentsService->delete($scheduleId);
    }
}
