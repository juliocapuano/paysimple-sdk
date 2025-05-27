<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\CustomerService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;

class CustomerServiceTest extends MockeryTestCase
{
    public function testGetCustomerSuccessfully()
    {
        // Mock the ApiClient
        $apiClientMock = Mockery::mock(ApiClient::class);

        // Expected customer ID and data
        $customerId = 123;
        $expectedData = ['Id' => $customerId, 'FirstName' => 'John', 'LastName' => 'Doe'];

        // Expected customer ID and data
        $customerId = 123;
        $expectedCustomerObject = (object) ['Id' => $customerId, 'FirstName' => 'John', 'LastName' => 'Doe'];

        // Configure the mock ApiClient
        $apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedCustomerObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);
        
        $apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        // Create an instance of CustomerService with the mocked ApiClient
        $customerService = new CustomerService($apiClientMock);

        // Call the method to be tested
        $customer = $customerService->get($customerId);

        // Assert that the returned customer data matches the expected data object
        $this->assertEquals($expectedCustomerObject, $customer);
    }

    public function testGetCustomerThrowsExceptionOnError()
    {
        // Mock the ApiClient
        $apiClientMock = Mockery::mock(ApiClient::class);

        // Customer ID for the test
        $customerId = 456;

        // Configure the mock ApiClient to simulate an error response
        // Configure the mock ApiClient to simulate an error response
        $errorMessages = ['The customer could not be found.'];
        $errorResponseData = ['errors' => $errorMessages]; // This must be an array

        $errorResponse = [
            'error' => true,
            'data' => $errorResponseData, // Ensure this is an array
            'meta' => (object)[
                'HttpStatus' => 400,
            ]
        ];

        $apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}")
            ->once()
            ->andReturn($errorResponse);

        // Ensure hasErrors() is also checked and returns true
        $apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        // Create an instance of CustomerService with the mocked ApiClient
        $customerService = new CustomerService($apiClientMock);

        // Expect the PaySimpleException to be thrown
        $this->expectException(\PaySimple\V4\Core\PaySimpleException::class);
        $this->expectExceptionMessage("The customer could not be found.");

        // Call the method that should throw the exception
        $customerService->get($customerId);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
