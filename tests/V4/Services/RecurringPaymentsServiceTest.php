<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\RecurringPaymentsService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\RecurringPaymentSchedule;
use PaySimple\V4\Entities\Payment;
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
        $scheduleInput = new RecurringPaymentSchedule();
        $scheduleInput->AccountId = 123;
        $scheduleInput->PaymentAmount = 50.00;
        $scheduleInput->StartDate = '2024-12-01';
        $scheduleInput->ExecutionFrequencyType = 'Monthly';
        // Populate other necessary fields for RecurringPaymentSchedule::toArray for a 'new' request

        $expectedApiRequestArray = $scheduleInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 1;
        $apiResponseData->ScheduleStatus = 'Active';
        $apiResponseData->PaymentAmount = 50.00;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('post')
            ->with('recurringpayment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->new($scheduleInput);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
        $this->assertEquals($apiResponseData->PaymentAmount, $returnedSchedule->PaymentAmount);
    }

    public function testNewRecurringPaymentThrowsExceptionOnError()
    {
        $scheduleInput = new RecurringPaymentSchedule();
        $scheduleInput->AccountId = 123;
        $scheduleInput->PaymentAmount = 50.00;
        $scheduleInput->StartDate = 'invalid-date';
        $scheduleInput->ExecutionFrequencyType = 'Monthly';
        // Populate other necessary fields

        $expectedApiRequestArray = $scheduleInput->toArray();

        $errorMessages = ['Invalid StartDate'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('recurringpayment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid StartDate');

        $this->recurringPaymentsService->new($scheduleInput);
    }

    public function testGetRecurringPaymentSuccessfully()
    {
        $scheduleId = 456;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $scheduleId;
        $apiResponseData->ScheduleStatus = 'Active';
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->get($scheduleId);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testListRecurringPaymentsSuccessfully()
    {
        $filters = ['status' => 'Active'];
        
        $schedule1StdClass = new stdClass();
        $schedule1StdClass->Id = 1;
        $schedule1StdClass->ScheduleStatus = 'Active';

        $schedule2StdClass = new stdClass();
        $schedule2StdClass->Id = 2;
        $schedule2StdClass->ScheduleStatus = 'Active';

        $apiResponseDataArray = [$schedule1StdClass, $schedule2StdClass];

        $this->apiClientMock->shouldReceive('get')
            ->with('recurringpayment', $filters)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseDataArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedules = $this->recurringPaymentsService->list($filters);

        $this->assertIsArray($returnedSchedules);
        $this->assertCount(2, $returnedSchedules);

        foreach ($returnedSchedules as $index => $scheduleEntity) {
            $this->assertInstanceOf(RecurringPaymentSchedule::class, $scheduleEntity);
            $this->assertEquals($apiResponseDataArray[$index]->Id, $scheduleEntity->Id);
            $this->assertEquals($apiResponseDataArray[$index]->ScheduleStatus, $scheduleEntity->ScheduleStatus);
        }
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testCustomerListRecurringPaymentsSuccessfully()
    {
        $customerId = 123;

        $schedule1StdClass = new stdClass();
        $schedule1StdClass->Id = 1;
        $schedule1StdClass->CustomerId = $customerId;
        $schedule1StdClass->ScheduleStatus = 'Active';

        $schedule2StdClass = new stdClass();
        $schedule2StdClass->Id = 2;
        $schedule2StdClass->CustomerId = $customerId;
        $schedule2StdClass->ScheduleStatus = 'Paused';

        $apiResponseDataArray = [$schedule1StdClass, $schedule2StdClass];

        $this->apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}/recurringpayment")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseDataArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedules = $this->recurringPaymentsService->customerList($customerId);

        $this->assertIsArray($returnedSchedules);
        $this->assertCount(2, $returnedSchedules);

        foreach ($returnedSchedules as $index => $scheduleEntity) {
            $this->assertInstanceOf(RecurringPaymentSchedule::class, $scheduleEntity);
            $this->assertEquals($apiResponseDataArray[$index]->Id, $scheduleEntity->Id);
            $this->assertEquals($apiResponseDataArray[$index]->ScheduleStatus, $scheduleEntity->ScheduleStatus);
            $this->assertEquals($apiResponseDataArray[$index]->CustomerId, $scheduleEntity->CustomerId);
        }
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testPaymentListForScheduleSuccessfully()
    {
        $scheduleId = 789;

        $payment1StdClass = new stdClass();
        $payment1StdClass->Id = 101;
        $payment1StdClass->Amount = 50.00;
        $payment1StdClass->Status = "Settled";

        $payment2StdClass = new stdClass();
        $payment2StdClass->Id = 102;
        $payment2StdClass->Amount = 50.00;
        $payment2StdClass->Status = "Posted";

        $apiResponseDataArray = [$payment1StdClass, $payment2StdClass];

        $this->apiClientMock->shouldReceive('get')
            ->with("recurringpayment/{$scheduleId}/payments")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseDataArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedPayments = $this->recurringPaymentsService->paymentList($scheduleId);

        $this->assertIsArray($returnedPayments);
        $this->assertCount(2, $returnedPayments);

        foreach ($returnedPayments as $index => $paymentEntity) {
            $this->assertInstanceOf(Payment::class, $paymentEntity);
            $this->assertEquals($apiResponseDataArray[$index]->Id, $paymentEntity->Id);
            $this->assertEquals($apiResponseDataArray[$index]->Amount, $paymentEntity->Amount);
            $this->assertEquals($apiResponseDataArray[$index]->Status, $paymentEntity->Status);
        }
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testUpdateRecurringPaymentSuccessfully()
    {
        $scheduleInput = new RecurringPaymentSchedule();
        $scheduleInput->Id = 123; // Schedule ID to update
        $scheduleInput->AccountId = 456;
        $scheduleInput->PaymentAmount = 55.00; // Updated amount
        $scheduleInput->StartDate = '2024-12-01';
        $scheduleInput->EndDate = '2025-12-01';
        $scheduleInput->ExecutionFrequencyType = 'Monthly';
        $scheduleInput->PaymentType = 'ACH'; // Example, add other required fields

        $expectedApiRequestArray = $scheduleInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 123;
        $apiResponseData->PaymentAmount = 55.00;
        $apiResponseData->ScheduleStatus = 'Active';
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with('recurringpayment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->update($scheduleInput);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->PaymentAmount, $returnedSchedule->PaymentAmount);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
    }

    public function testUpdateRecurringPaymentThrowsExceptionOnError()
    {
        $scheduleInput = new RecurringPaymentSchedule();
        $scheduleInput->Id = 123;
        $scheduleInput->PaymentAmount = -10.00; // Invalid amount
        // Populate other necessary fields for RecurringPaymentSchedule::toArray
        $scheduleInput->AccountId = 456;
        $scheduleInput->StartDate = '2024-12-01';
        $scheduleInput->EndDate = '2025-12-01';
        $scheduleInput->ExecutionFrequencyType = 'Monthly';
        $scheduleInput->PaymentType = 'ACH';

        $expectedApiRequestArray = $scheduleInput->toArray();

        $errorMessages = ['Invalid PaymentAmount'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with('recurringpayment', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid PaymentAmount');

        $this->recurringPaymentsService->update($scheduleInput);
    }

    public function testSuspendScheduleSuccessfully()
    {
        $scheduleId = 123;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $scheduleId;
        $apiResponseData->ScheduleStatus = 'Suspended';
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/suspend", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->suspend($scheduleId);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testPauseScheduleSuccessfully()
    {
        $scheduleId = 123;
        $endDate = new DateTime('2024-12-31');
        $formattedEndDate = $endDate->format('Y-m-d');
        
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $scheduleId;
        $apiResponseData->ScheduleStatus = 'Suspended'; // Or "Paused" based on actual API
        $apiResponseData->PauseUntilDate = $formattedEndDate;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/pause?enddate={$formattedEndDate}", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->pause($scheduleId, $endDate);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
        $this->assertEquals($apiResponseData->PauseUntilDate, $returnedSchedule->PauseUntilDate);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testResumeScheduleSuccessfully()
    {
        $scheduleId = 123;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $scheduleId;
        $apiResponseData->ScheduleStatus = 'Active';
        $apiResponseData->PauseUntilDate = null;
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('put')
            ->with("recurringpayment/{$scheduleId}/resume", [])
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedSchedule = $this->recurringPaymentsService->resume($scheduleId);
        $this->assertInstanceOf(RecurringPaymentSchedule::class, $returnedSchedule);
        $this->assertEquals($apiResponseData->Id, $returnedSchedule->Id);
        $this->assertEquals($apiResponseData->ScheduleStatus, $returnedSchedule->ScheduleStatus);
        $this->assertNull($returnedSchedule->PauseUntilDate);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
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
