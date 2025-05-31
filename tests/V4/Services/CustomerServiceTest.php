<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Entities\Customer;
use PaySimple\V4\Entities\Address;
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
        $expectedRawData = (object) [
            'Id' => $customerId,
            'FirstName' => 'John',
            'LastName' => 'Doe',
            'BillingAddress' => (object)[
                'StreetAddress1' => '123 Main St',
                'City' => 'Anytown',
                'StateCode' => 'CO', // Or StateProvince
                'ZipCode' => '80000'  // Or PostalCode
            ],
            'ShippingAddress' => (object)[
                'StreetAddress1' => '456 Ship Ave',
                'City' => 'Shiptown',
                'StateCode' => 'TX',
                'ZipCode' => '70000'
            ],
            'ShippingSameAsBilling' => false
        ];

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

        $this->assertInstanceOf(Address::class, $customer->BillingAddress);
        $this->assertEquals($expectedRawData->BillingAddress->StreetAddress1, $customer->BillingAddress->StreetAddress1);
        $this->assertEquals($expectedRawData->BillingAddress->City, $customer->BillingAddress->City);
        $this->assertEquals($expectedRawData->BillingAddress->StateCode, $customer->BillingAddress->StateCode);
        $this->assertEquals($expectedRawData->BillingAddress->ZipCode, $customer->BillingAddress->ZipCode);

        $this->assertInstanceOf(Address::class, $customer->ShippingAddress);
        $this->assertEquals($expectedRawData->ShippingAddress->StreetAddress1, $customer->ShippingAddress->StreetAddress1);
        $this->assertEquals(false, $customer->ShippingSameAsBilling);

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
        $customerInput->ShippingSameAsBilling = true;

        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '123 Main St';
        $billingAddressInput->City = 'Anytown';
        $billingAddressInput->StateCode = 'CO';
        $billingAddressInput->ZipCode = '80000';
        $customerInput->BillingAddress = $billingAddressInput;

        $expectedApiRequestArray = $customerInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 12345;
        $apiResponseData->FirstName = "Test";
        $apiResponseData->LastName = "User";
        $apiResponseData->Email = "test.user@example.com";
        $apiResponseData->ShippingSameAsBilling = true;
        $apiResponseData->BillingAddress = (object) [
            'StreetAddress1' => '123 Main St',
            'City' => 'Anytown',
            'StateCode' => 'CO',
            'ZipCode' => '80000'
        ];
        // No ShippingAddress in response if ShippingSameAsBilling is true

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
        $this->assertTrue($returnedCustomer->ShippingSameAsBilling);
        $this->assertInstanceOf(Address::class, $returnedCustomer->BillingAddress);
        $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedCustomer->BillingAddress->StreetAddress1);
        $this->assertNull($returnedCustomer->ShippingAddress); // Because ShippingSameAsBilling is true
    }

    public function testNewCustomerThrowsExceptionOnError()
    {
        $customerInput = new Customer();
        $customerInput->FirstName = "Error";
        $customerInput->LastName = "Test";
        // Ensure BillingAddress is an Address entity for toArray() to work correctly
        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '123 Main St';
        $billingAddressInput->City = 'Anytown';
        $billingAddressInput->StateCode = 'CO';
        $billingAddressInput->ZipCode = '80000';
        $customerInput->BillingAddress = $billingAddressInput;
        $customerInput->ShippingSameAsBilling = true;

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
        $customerInput->ShippingSameAsBilling = true;

        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '456 New St';
        $billingAddressInput->City = 'Newtown';
        $billingAddressInput->StateCode = 'NY';
        $billingAddressInput->ZipCode = '10001';
        $customerInput->BillingAddress = $billingAddressInput;
        // No ShippingAddress needed as ShippingSameAsBilling is true

        $expectedApiRequestArray = $customerInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 12345;
        $apiResponseData->FirstName = "Updated Test";
        $apiResponseData->LastName = "Updated User";
        $apiResponseData->Email = "updated.user@example.com";
        $apiResponseData->ShippingSameAsBilling = true;
        $apiResponseData->BillingAddress = (object) [
            'StreetAddress1' => '456 New St',
            'City' => 'Newtown',
            'StateCode' => 'NY', // Or StateProvince
            'ZipCode' => '10001'  // Or PostalCode
        ];
        // ... other properties returned by API for an updated customer

        $this->apiClientMock->shouldReceive('put')
            ->with('customer', $expectedApiRequestArray)
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
        $this->assertInstanceOf(Address::class, $returnedCustomer->BillingAddress);
        $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedCustomer->BillingAddress->StreetAddress1);
        $this->assertNull($returnedCustomer->ShippingAddress);
    }

    public function testUpdateCustomerThrowsExceptionOnError()
    {
        $customerInput = new Customer();
        $customerInput->Id = 12345; // ID of the customer to update
        $customerInput->FirstName = "Error Update";
        // Ensure BillingAddress is an Address entity for toArray() to work correctly
        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '456 New St';
        $billingAddressInput->City = 'Newtown';
        $billingAddressInput->StateCode = 'NY';
        $billingAddressInput->ZipCode = '10001';
        $customerInput->BillingAddress = $billingAddressInput;
        $customerInput->ShippingSameAsBilling = true;

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
        $customer1StdClass->BillingAddress = (object)[
            'StreetAddress1' => '789 List St',
            'City' => 'Listville',
            'StateCode' => 'LS',
            'ZipCode' => '50000'
        ];
        $customer1StdClass->ShippingSameAsBilling = true;


        $customer2StdClass = new stdClass();
        $customer2StdClass->Id = 2;
        $customer2StdClass->FirstName = "List";
        $customer2StdClass->LastName = "UserTwo";
        $customer2StdClass->Email = "test@example.com";
        $customer2StdClass->BillingAddress = (object)[
            'StreetAddress1' => '101 Array Ave',
            'City' => 'Arraysburg',
            'StateCode' => 'AR',
            'ZipCode' => '60000'
        ];
        $customer2StdClass->ShippingAddress = (object)[
            'StreetAddress1' => '102 Ship Rd',
            'City' => 'Shipton',
            'StateCode' => 'SH',
            'ZipCode' => '60001'
        ];
        $customer2StdClass->ShippingSameAsBilling = false;


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
            $expectedStdClass = $apiResponseDataArray[$index];
            $this->assertEquals($expectedStdClass->Id, $customerEntity->Id);
            $this->assertEquals($expectedStdClass->FirstName, $customerEntity->FirstName);
            $this->assertEquals($expectedStdClass->LastName, $customerEntity->LastName);
            $this->assertEquals($expectedStdClass->Email, $customerEntity->Email);

            if (isset($expectedStdClass->BillingAddress)) {
                $this->assertInstanceOf(Address::class, $customerEntity->BillingAddress);
                $this->assertEquals($expectedStdClass->BillingAddress->StreetAddress1, $customerEntity->BillingAddress->StreetAddress1);
            }
             if (isset($expectedStdClass->ShippingAddress)) {
                $this->assertInstanceOf(Address::class, $customerEntity->ShippingAddress);
                $this->assertEquals($expectedStdClass->ShippingAddress->StreetAddress1, $customerEntity->ShippingAddress->StreetAddress1);
            }
            $this->assertEquals($expectedStdClass->ShippingSameAsBilling, $customerEntity->ShippingSameAsBilling);
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
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
