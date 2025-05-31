<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\CreditCard;
use PaySimple\V4\Entities\ACHAccount;
use PaySimple\V4\Entities\Address;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use stdClass;

class AccountServiceTest extends MockeryTestCase
{
    protected $apiClientMock;
    protected $accountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->accountService = new AccountService($this->apiClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testNewCreditCardSuccessfully()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->CustomerId = 123;
        $creditCardInput->Token = 'tok_xxxxxxxx';

        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '123 Test St';
        $billingAddressInput->City = 'Testerville';
        $billingAddressInput->StateCode = 'TS';
        $billingAddressInput->ZipCode = '12345';
        $creditCardInput->BillingAddress = $billingAddressInput;

        $expectedApiRequestArray = $creditCardInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 1;
        $apiResponseData->Issuer = 'Visa';
        $apiResponseData->LastFour = '1234';
        $apiResponseData->CustomerId = 123;
        $apiResponseData->BillingAddress = (object)[
            'StreetAddress1' => '123 Test St',
            'City' => 'Testerville',
            'StateCode' => 'TS', // Or StateProvince
            'ZipCode' => '12345'   // Or PostalCode
        ];
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('post')
            ->with('account/creditcard', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCard = $this->accountService->newCreditCard($creditCardInput);
        $this->assertInstanceOf(CreditCard::class, $returnedCard);
        $this->assertEquals($apiResponseData->Id, $returnedCard->Id);
        $this->assertEquals($apiResponseData->Issuer, $returnedCard->Issuer);
        $this->assertEquals($apiResponseData->LastFour, $returnedCard->LastFour);
        $this->assertInstanceOf(Address::class, $returnedCard->BillingAddress);
        $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedCard->BillingAddress->StreetAddress1);
        $this->assertEquals($apiResponseData->BillingAddress->City, $returnedCard->BillingAddress->City);
    }

    public function testNewCreditCardThrowsExceptionOnError()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->CustomerId = 123;
        $creditCardInput->Token = 'tok_invalid';
        // If BillingAddress were required for this specific error, it would be set here as an Address entity
        // For this test, assuming it's not strictly required to trigger "Invalid token"
        // $billingAddressInput = new Address();
        // $billingAddressInput->StreetAddress1 = '123 Error St';
        // $creditCardInput->BillingAddress = $billingAddressInput;


        $expectedApiRequestArray = $creditCardInput->toArray();

        $errorMessages = ['Invalid token'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('account/creditcard', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid token');

        $this->accountService->newCreditCard($creditCardInput);
    }

    public function testGetCreditCardSuccessfully()
    {
        $accountId = 42;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $accountId;
        $apiResponseData->Issuer = "MasterCard";
        $apiResponseData->LastFour = "5678";
        $apiResponseData->CustomerId = 123;
        $apiResponseData->BillingAddress = (object)[
            'StreetAddress1' => '456 Card Ave',
            'City' => 'Cardville',
            'StateCode' => 'CA',
            'ZipCode' => '90210'
        ];
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('get')
            ->with(sprintf("account/creditcard/%s", $accountId))
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCard = $this->accountService->getCreditCard($accountId);
        $this->assertInstanceOf(CreditCard::class, $returnedCard);
        $this->assertEquals($apiResponseData->Id, $returnedCard->Id);
        $this->assertEquals($apiResponseData->Issuer, $returnedCard->Issuer);
        $this->assertEquals($apiResponseData->LastFour, $returnedCard->LastFour);
        $this->assertInstanceOf(Address::class, $returnedCard->BillingAddress);
        $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedCard->BillingAddress->StreetAddress1);
        $this->assertEquals($apiResponseData->BillingAddress->City, $returnedCard->BillingAddress->City);
    }

    public function testGetCreditCardThrowsExceptionOnError()
    {
        $accountId = 73;
        $errorMessages = ['Credit card account not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with(sprintf("account/creditcard/%s", $accountId))
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Credit card account not found');

        $this->accountService->getCreditCard($accountId);
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testUpdateCreditCardSuccessfully()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->Id = 73;
        $creditCardInput->ExpirationDate = "1225";
        $creditCardInput->BillingZipCode = "80302"; // Update standalone zip

        // If the API allows updating the full BillingAddress object during a card update:
        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = "789 Updated Rd";
        $billingAddressInput->City = "Updateville";
        $billingAddressInput->StateCode = "UP";
        $billingAddressInput->ZipCode = "54321"; // This would override BillingZipCode if Address obj takes precedence
        // $creditCardInput->BillingAddress = $billingAddressInput; // Uncomment if API supports full address update

        $expectedApiRequestArray = $creditCardInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 73;
        $apiResponseData->Issuer = "Visa";
        $apiResponseData->LastFour = "1234";
        $apiResponseData->ExpirationDate = "12/2025";
        $apiResponseData->BillingZipCode = "80302"; // Reflects the standalone zip update
        $apiResponseData->IsDefault = false;
        // If API returns full address for card updates:
        $apiResponseData->BillingAddress = (object)[
            'StreetAddress1' => '789 Updated Rd', // Or original if not updated
            'City' => 'Updateville',
            'StateCode' => 'UP',
            'ZipCode' => '54321' // Or $creditCardInput->BillingZipCode if that's what API returns
        ];


        $this->apiClientMock->shouldReceive('put')
            ->with('account/creditcard', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedCard = $this->accountService->updateCreditCard($creditCardInput);
        $this->assertInstanceOf(CreditCard::class, $returnedCard);
        $this->assertEquals($apiResponseData->Id, $returnedCard->Id);
        $this->assertEquals("12/2025", $returnedCard->ExpirationDate);
        $this->assertEquals($apiResponseData->BillingZipCode, $returnedCard->BillingZipCode);

        // If BillingAddress is expected in response:
        if (isset($apiResponseData->BillingAddress)) {
            $this->assertInstanceOf(Address::class, $returnedCard->BillingAddress);
            $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedCard->BillingAddress->StreetAddress1);
        }
    }

    public function testUpdateCreditCardThrowsExceptionOnError()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->Id = 73;
        $creditCardInput->ExpirationDate = "0000"; // Invalid data
        // If BillingAddress were relevant to this specific error, it would be set here as an Address entity
        // $billingAddressInput = new Address();
        // $billingAddressInput->StreetAddress1 = "123 Error St";
        // $creditCardInput->BillingAddress = $billingAddressInput;

        $expectedApiRequestArray = $creditCardInput->toArray();

        $errorMessages = ['Invalid expiration date'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with('account/creditcard', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid expiration date');

        $this->accountService->updateCreditCard($creditCardInput);
    }

    public function testGetAchSuccessfully()
    {
        $accountId = 123;
        $apiResponseData = new stdClass();
        $apiResponseData->Id = $accountId;
        $apiResponseData->AccountNumber = 'xxxx1234'; // Masked
        $apiResponseData->BankName = 'Test Bank';
        $apiResponseData->IsCheckingAccount = true; // API returns boolean
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('get')
            ->with("account/ach/{$accountId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedAccount = $this->accountService->getAch($accountId);
        $this->assertInstanceOf(ACHAccount::class, $returnedAccount);
        $this->assertEquals($apiResponseData->Id, $returnedAccount->Id);
        $this->assertEquals($apiResponseData->BankName, $returnedAccount->BankName);
        $this->assertEquals('Checking', $returnedAccount->AccountType); // Derived by fromStdClass
        $this->assertTrue($returnedAccount->IsCheckingAccount);

        if (isset($apiResponseData->BillingAddress)) {
            $this->assertInstanceOf(Address::class, $returnedAccount->BillingAddress);
            $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedAccount->BillingAddress->StreetAddress1);
        }
    }

    public function testGetAchThrowsExceptionOnError()
    {
        $accountId = 456;
        $errorMessages = ['Account not found'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('get')
            ->with("account/ach/{$accountId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 404]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Account not found');

        $this->accountService->getAch($accountId);
        // No significant changes needed other than ensuring it uses class properties, which it already does.
    }

    public function testNewAchSuccessfully()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->CustomerId = 123;
        $achAccountInput->RoutingNumber = "123456789";
        $achAccountInput->AccountNumber = "987654321";
        $achAccountInput->BankName = "Test Bank ACH";
        $achAccountInput->IsCheckingAccount = true;

        $billingAddressInput = new Address();
        $billingAddressInput->StreetAddress1 = '789 ACH St';
        $billingAddressInput->City = 'ACHville';
        $billingAddressInput->StateCode = 'AC';
        $billingAddressInput->ZipCode = '67890';
        $achAccountInput->BillingAddress = $billingAddressInput;

        $expectedApiRequestArray = $achAccountInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 2;
        $apiResponseData->CustomerId = 123;
        $apiResponseData->LastFour = "4321";
        $apiResponseData->BankName = "Test Bank ACH";
        $apiResponseData->IsCheckingAccount = true;
        $apiResponseData->BillingAddress = (object)[
            'StreetAddress1' => '789 ACH St',
            'City' => 'ACHville',
            'StateCode' => 'AC',
            'ZipCode' => '67890'
        ];
        // ... other properties returned by API

        $this->apiClientMock->shouldReceive('post')
            ->with('account/ach', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedAccount = $this->accountService->newAch($achAccountInput);
        $this->assertInstanceOf(ACHAccount::class, $returnedAccount);
        $this->assertEquals($apiResponseData->Id, $returnedAccount->Id);
        $this->assertEquals($apiResponseData->BankName, $returnedAccount->BankName);
        $this->assertTrue($returnedAccount->IsCheckingAccount);
        $this->assertInstanceOf(Address::class, $returnedAccount->BillingAddress);
        $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedAccount->BillingAddress->StreetAddress1);
    }

    public function testNewAchThrowsExceptionOnError()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->CustomerId = 123;
        $achAccountInput->RoutingNumber = "000000000"; // Invalid
        // If BillingAddress were required for this specific error, it would be set here as an Address entity
        // For this test, assuming it's not strictly required to trigger "Invalid routing number"
        // $billingAddressInput = new Address();
        // $billingAddressInput->StreetAddress1 = '123 Error St';
        // $achAccountInput->BillingAddress = $billingAddressInput;


        $expectedApiRequestArray = $achAccountInput->toArray();

        $errorMessages = ['Invalid routing number'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('account/ach', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid routing number');

        $this->accountService->newAch($achAccountInput);
    }

    public function testUpdateAchSuccessfully()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->Id = 73;
        $achAccountInput->IsCheckingAccount = false; // Update to Savings
        // Note: PaySimple API for "Update ACH Account" only allows updating IsCheckingAccount and IsDefault.
        // BillingAddress is NOT updatable via this endpoint.
        // So, we don't set $achAccountInput->BillingAddress here for an update request.

        $expectedApiRequestArray = $achAccountInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 73;
        $apiResponseData->IsCheckingAccount = false;
        $apiResponseData->AccountType = "Savings";
        $apiResponseData->LastFour = "4321";
        $apiResponseData->BankName = "Test Bank ACH";
        // If the API response for an update *includes* the address, mock it here:
        $apiResponseData->BillingAddress = (object)[
            'StreetAddress1' => 'Original ACH St', // Assuming original address is returned
            'City' => 'Originalville',
            'StateCode' => 'OR',
            'ZipCode' => '13579'
        ];


        $this->apiClientMock->shouldReceive('put')
            ->with('account/ach', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $apiResponseData,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $returnedAccount = $this->accountService->updateAch($achAccountInput);
        $this->assertInstanceOf(ACHAccount::class, $returnedAccount);
        $this->assertEquals($apiResponseData->Id, $returnedAccount->Id);
        $this->assertEquals('Savings', $returnedAccount->AccountType);
        $this->assertFalse($returnedAccount->IsCheckingAccount);

        // Assert BillingAddress if it's expected in the response
        if (isset($apiResponseData->BillingAddress)) {
            $this->assertInstanceOf(Address::class, $returnedAccount->BillingAddress);
            $this->assertEquals($apiResponseData->BillingAddress->StreetAddress1, $returnedAccount->BillingAddress->StreetAddress1);
        }
    }

    public function testUpdateAchThrowsExceptionOnError()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->Id = 73;
        $achAccountInput->IsCheckingAccount = null; // Example of missing required field for an update
        // If BillingAddress were relevant to this specific error, it would be set here as an Address entity
        // $billingAddressInput = new Address();
        // $billingAddressInput->StreetAddress1 = '123 Error St';
        // $achAccountInput->BillingAddress = $billingAddressInput;

        $expectedApiRequestArray = $achAccountInput->toArray();

        $errorMessages = ['Invalid data for ACH update'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('put')
            ->with('account/ach', $expectedApiRequestArray)
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid data for ACH update');

        $this->accountService->updateAch($achAccountInput);
    }

    public function testDeleteAchSuccessfully()
    {
        $accountId = 888;

        $this->apiClientMock->shouldReceive('delete')
            ->with("account/ach/{$accountId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => null,
                'meta' => (object)['HttpStatus' => 204]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->accountService->deleteAch($accountId);
        $this->assertTrue($result);
    }

    public function testDeleteAchThrowsExceptionOnError()
    {
        $accountId = 999;
        $errorMessages = ['Cannot delete ACH account with recent activity'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('delete')
            ->with("account/ach/{$accountId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Cannot delete ACH account with recent activity');

        $this->accountService->deleteAch($accountId);
    }

    public function testDeleteCreditCardSuccessfully()
    {
        $accountId = 789;

        $this->apiClientMock->shouldReceive('delete')
            ->with("account/creditcard/{$accountId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => null, // Or (object)[] depending on actual API response for 204
                'meta' => (object)['HttpStatus' => 204]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->accountService->deleteCreditCard($accountId);
        $this->assertTrue($result);
    }

    public function testDeleteCreditCardThrowsExceptionOnError()
    {
        $accountId = 101;
        $errorMessages = ['Deletion failed'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('delete')
            ->with("account/creditcard/{$accountId}")
            ->once()
            ->andReturn([
                'error' => true,
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Deletion failed');

        $this->accountService->deleteCreditCard($accountId);
    }
}
