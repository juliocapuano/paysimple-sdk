<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Entities\Customer;
use PaySimple\V4\Core\PaySimpleException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use stdClass;

class CustomerServiceTest extends MockeryTestCase
{
    protected $apiClientMock;
    protected $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->customerService = new CustomerService($this->apiClientMock);
    }

    public function testGetCustomerSuccessfully()
    {
        // Expected customer ID and data
        $customerId = 123;
        // $expectedData = ['Id' => $customerId, 'FirstName' => 'John', 'LastName' => 'Doe']; // Duplicate, removed

        // Expected raw data from API mock
        $expectedRawData = (object) ['Id' => $customerId, 'FirstName' => 'John', 'LastName' => 'Doe'];

        // Configure the mock ApiClient
        $this->apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedRawData,
                'meta' => (object)['HttpStatus' => 200]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        // Call the method to be tested
        $customer = $this->customerService->get($customerId);

        // Assert that the returned customer data matches the expected data object
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($expectedRawData->Id, $customer->Id);
        $this->assertEquals($expectedRawData->FirstName, $customer->FirstName);
        $this->assertEquals($expectedRawData->LastName, $customer->LastName);
    }

    public function testGetCustomerThrowsExceptionOnError()
    {
        // Customer ID for the test
        $customerId = 456;

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

        $this->apiClientMock->shouldReceive('get')
            ->with("customer/{$customerId}")
            ->once()
            ->andReturn($errorResponse);

        // Ensure hasErrors() is also checked and returns true
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        // Expect the PaySimpleException to be thrown
        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage("The customer could not be found.");

        // Call the method that should throw the exception
        $this->customerService->get($customerId);
    }

    public function testNewCustomerSuccessfully()
    {
        $customerInput = new Customer();
        $customerInput->FirstName = "Test";
        $customerInput->LastName = "User";
        $customerInput->Email = "test.user@example.com";
        // Add other necessary properties for a new customer based on Customer::toArray() logic
        $customerInput->ShippingSameAsBilling = true; 
        $customerInput->BillingAddress = (object) ['StreetAddress1' => '123 Main St', 'City' => 'Anytown', 'StateCode' => 'CO', 'ZipCode' => '80000'];


        $expectedApiRequestArray = $customerInput->toArray(); // This is an array

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 12345;
        $apiResponseData->FirstName = "Test";
        $apiResponseData->LastName = "User";
        $apiResponseData->Email = "test.user@example.com";
        // ... other properties returned by API for a new customer
        $apiResponseData->ShippingSameAsBilling = true;
        $apiResponseData->BillingAddress = (object) ['StreetAddress1' => '123 Main St', 'City' => 'Anytown', 'StateCode' => 'CO', 'ZipCode' => '80000'];


        $this->apiClientMock->shouldReceive('post')
            ->with('customer', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 201]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCustomer = $this->customerService->new($customerInput);

        $this->assertInstanceOf(Customer::class, $returnedCustomer);
        $this->assertEquals($apiResponseData->Id, $returnedCustomer->Id);
        $this->assertEquals($apiResponseData->FirstName, $returnedCustomer->FirstName);
        $this->assertEquals($apiResponseData->LastName, $returnedCustomer->LastName);
        $this->assertEquals($apiResponseData->Email, $returnedCustomer->Email);
        // Assert other relevant properties
    }

    public function testNewCustomerThrowsExceptionOnError()
    {
        $customerInput = new Customer();
        $customerInput->FirstName = "Error";
        $customerInput->LastName = "Test";
        // Other properties as needed to form a valid request array
        $customerInput->ShippingSameAsBilling = true; 
        $customerInput->BillingAddress = (object) ['StreetAddress1' => '123 Main St', 'City' => 'Anytown', 'StateCode' => 'CO', 'ZipCode' => '80000'];

        $expectedApiRequestArray = $customerInput->toArray();

        $errorMessages = ['Invalid data provided'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('customer', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid data provided');

        $this->customerService->new($customerInput);
    }

    public function testUpdateCustomerSuccessfully()
    {
        $customerInput = new Customer();
        $customerInput->Id = 12345; // ID of the customer to update
        $customerInput->FirstName = "Updated Test";
        $customerInput->LastName = "Updated User";
        $customerInput->Email = "updated.user@example.com";
        // Add other necessary properties for an update based on Customer::toArray() logic
        $customerInput->ShippingSameAsBilling = true; 
        $customerInput->BillingAddress = (object) ['StreetAddress1' => '456 New St', 'City' => 'Newtown', 'StateCode' => 'NY', 'ZipCode' => '10001'];

        $expectedApiRequestArray = $customerInput->toArray(); // This is an array

        // API response might be the same as the input, or just a success indicator
        // For this test, let's assume it returns the updated customer object
        $apiResponseData = new stdClass();
        $apiResponseData->Id = 12345;
        $apiResponseData->FirstName = "Updated Test";
        $apiResponseData->LastName = "Updated User";
        $apiResponseData->Email = "updated.user@example.com";
        $apiResponseData->ShippingSameAsBilling = true;
        $apiResponseData->BillingAddress = (object) ['StreetAddress1' => '456 New St', 'City' => 'Newtown', 'StateCode' => 'NY', 'ZipCode' => '10001'];
        // ... other properties returned by API for an updated customer

        $this->apiClientMock->shouldReceive('put')
            ->with('customer', $expectedApiRequestArray) // Endpoint for update might be 'customer/ID' or just 'customer' with ID in body
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCustomer = $this->customerService->update($customerInput);

        $this->assertInstanceOf(Customer::class, $returnedCustomer);
        $this->assertEquals($apiResponseData->Id, $returnedCustomer->Id);
        $this->assertEquals($apiResponseData->FirstName, $returnedCustomer->FirstName);
        $this->assertEquals($apiResponseData->Email, $returnedCustomer->Email);
        // Assert other relevant properties
    }

    public function testUpdateCustomerThrowsExceptionOnError()
    {
        $customerInput = new Customer();
        $customerInput->Id = 12345; // ID of the customer to update
        $customerInput->FirstName = "Error Update";
        // Other properties as needed for a valid request for update
        $customerInput->ShippingSameAsBilling = true; 
        $customerInput->BillingAddress = (object) ['StreetAddress1' => '456 New St', 'City' => 'Newtown', 'StateCode' => 'NY', 'ZipCode' => '10001'];


        $expectedApiRequestArray = $customerInput->toArray();

        $errorMessages = ['Update failed due to validation error'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with('customer', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Update failed due to validation error');

        $this->customerService->update($customerInput);
    }

    public function testListCustomersSuccessfully()
    {
        $filterParams = ['email' => 'test@example.com'];

        $customer1StdClass = new stdClass();
        $customer1StdClass->Id = 1;
        $customer1StdClass->FirstName = "List";
        $customer1StdClass->LastName = "UserOne";
        $customer1StdClass->Email = "test@example.com";

        $customer2StdClass = new stdClass();
        $customer2StdClass->Id = 2;
        $customer2StdClass->FirstName = "List";
        $customer2StdClass->LastName = "UserTwo";
        $customer2StdClass->Email = "test@example.com";

        $apiResponseDataArray = [$customer1StdClass, $customer2StdClass];

        $this->apiClientMock->shouldReceive('get')
            ->with('customer', $filterParams)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseDataArray,
                'meta' => (object)['HttpStatus' => 200]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCustomers = $this->customerService->list($filterParams);

        $this->assertIsArray($returnedCustomers);
        $this->assertCount(2, $returnedCustomers);

        foreach ($returnedCustomers as $index => $customerEntity) {
            $this->assertInstanceOf(Customer::class, $customerEntity);
            $this->assertEquals($apiResponseDataArray[$index]->Id, $customerEntity->Id);
            $this->assertEquals($apiResponseDataArray[$index]->FirstName, $customerEntity->FirstName);
            $this->assertEquals($apiResponseDataArray[$index]->LastName, $customerEntity->LastName);
            $this->assertEquals($apiResponseDataArray[$index]->Email, $customerEntity->Email);
        }
    }

    public function testListCustomersThrowsExceptionOnError()
    {
        $filterParams = ['status' => 'invalid'];
        $errorMessages = ['Invalid filter parameter'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with('customer', $filterParams)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);
        
        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid filter parameter');

        $this->customerService->list($filterParams);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
