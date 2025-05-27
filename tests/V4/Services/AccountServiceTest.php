<?php

namespace PaySimple\Tests\V4\Services;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Core\PaySimpleException;
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
        $inputData = ['CustomerId' => 123, 'Token' => 'tok_xxxxxxxx'];
        $expectedResponseObject = (object)['Id' => 1, 'Issuer' => 'Visa', 'LastFour' => '1234'];

        $this->apiClientMock->shouldReceive('post')
            ->with('account/creditcard', $inputData)
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 201]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->accountService->newCreditCard($inputData);
        $this->assertEquals($expectedResponseObject, $result);
    }

    public function testNewCreditCardThrowsExceptionOnError()
    {
        $inputData = ['CustomerId' => 123, 'Token' => 'tok_invalid'];
        $errorMessages = ['Invalid token'];
        $errorResponseData = ['errors' => $errorMessages];

        $this->apiClientMock->shouldReceive('post')
            ->with('account/creditcard', $inputData)
            ->once()
            ->andReturn([
                'error' => true, // This can also be derived from hasErrors()
                'data' => $errorResponseData,
                'meta' => (object)['HttpStatus' => 400]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(true);

        $this->expectException(PaySimpleException::class);
        $this->expectExceptionMessage('Invalid token');

        $this->accountService->newCreditCard($inputData);
    }

    public function testGetAchSuccessfully()
    {
        $accountId = 123;
        $expectedResponseObject = (object)['Id' => $accountId, 'AccountNumber' => 'xxxx1234', 'BankName' => 'Test Bank'];

        $this->apiClientMock->shouldReceive('get')
            ->with("account/ach/{$accountId}")
            ->once()
            ->andReturn([
                'error' => false,
                'data' => $expectedResponseObject,
                'meta' => (object)['HttpStatus' => 200]
            ]);

        $this->apiClientMock->shouldReceive('hasErrors')->andReturn(false);

        $result = $this->accountService->getAch($accountId);
        $this->assertEquals($expectedResponseObject, $result);
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
