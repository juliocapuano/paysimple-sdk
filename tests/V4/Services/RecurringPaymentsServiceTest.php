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
}
