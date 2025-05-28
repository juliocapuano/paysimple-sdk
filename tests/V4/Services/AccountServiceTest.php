<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\CreditCard;
use PaySimple\V4\Entities\ACHAccount;
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
        // Populate other necessary fields for CreditCard::toArray for a 'new' request
        // e.g., $creditCardInput->BillingAddress = (object)[...];

        $expectedApiRequestArray = $creditCardInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 1;
        $apiResponseData->Issuer = 'Visa';
        $apiResponseData->LastFour = '1234';
        $apiResponseData->CustomerId = 123;
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
    }

    public function testNewCreditCardThrowsExceptionOnError()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->CustomerId = 123;
        $creditCardInput->Token = 'tok_invalid';
        // Populate other necessary fields

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
    }

    public function testUpdateCreditCardSuccessfully()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->Id = 73; // Must match an existing ID
        $creditCardInput->ExpirationDate = "1225"; // MMYY
        $creditCardInput->BillingZipCode = "80302";
        // The API only allows ExpirationDate and BillingZipCode to be updated
        // via this method, plus IsDefault (not tested here for brevity).
        // CustomerId is not required in the body for update.

        $expectedApiRequestArray = $creditCardInput->toArray(); 

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 73;
        $apiResponseData->Issuer = "Visa"; // Assuming original issuer
        $apiResponseData->LastFour = "1234"; // Assuming original last four
        $apiResponseData->ExpirationDate = "12/2025"; // API might return with slash
        $apiResponseData->BillingZipCode = "80302";
        $apiResponseData->IsDefault = false;

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
        // ExpirationDate might be MMYY in entity after fromStdClass if it normalizes
        // For now, assume it matches what fromStdClass produces from "12/2025"
        $this->assertEquals("12/2025", $returnedCard->ExpirationDate); 
        $this->assertEquals($apiResponseData->BillingZipCode, $returnedCard->BillingZipCode);
    }

    public function testUpdateCreditCardThrowsExceptionOnError()
    {
        $creditCardInput = new CreditCard();
        $creditCardInput->Id = 73;
        $creditCardInput->ExpirationDate = "0000"; // Invalid data
        
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
        // No significant changes needed other than ensuring it uses class properties, which it does.
    }

    public function testNewAchSuccessfully()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->CustomerId = 123;
        $achAccountInput->RoutingNumber = "123456789";
        $achAccountInput->AccountNumber = "987654321";
        $achAccountInput->BankName = "Test Bank ACH";
        $achAccountInput->IsCheckingAccount = true;
        // Populate other necessary fields for ACHAccount::toArray for a 'new' request

        $expectedApiRequestArray = $achAccountInput->toArray();

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 2;
        $apiResponseData->CustomerId = 123;
        $apiResponseData->LastFour = "4321";
        $apiResponseData->BankName = "Test Bank ACH";
        $apiResponseData->IsCheckingAccount = true;
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
    }

    public function testNewAchThrowsExceptionOnError()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->CustomerId = 123;
        $achAccountInput->RoutingNumber = "000000000"; // Invalid
        // Populate other necessary fields

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
        $achAccountInput->Id = 73; // Must match an existing ID
        $achAccountInput->IsCheckingAccount = false; // Update to Savings
        // The API only allows IsCheckingAccount and IsDefault to be updated
        // via this method. CustomerId is not required in the body for update.

        $expectedApiRequestArray = $achAccountInput->toArray(); 

        $apiResponseData = new stdClass();
        $apiResponseData->Id = 73;
        $apiResponseData->IsCheckingAccount = false;
        $apiResponseData->AccountType = "Savings"; // Reflects the change
        $apiResponseData->LastFour = "4321"; // Assuming original last four
        $apiResponseData->BankName = "Test Bank ACH"; // Assuming original bank name

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
    }

    public function testUpdateAchThrowsExceptionOnError()
    {
        $achAccountInput = new ACHAccount();
        $achAccountInput->Id = 73;
        $achAccountInput->IsCheckingAccount = null; // Example of missing required field for an update if API enforced it
        
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
