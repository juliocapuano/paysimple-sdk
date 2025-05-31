<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\CreditCard;
use PaySimple\V4\Entities\ACHAccount;

class AccountService extends Service
{

    // --- Credit Card ---
    /**
     * Creates a new Credit Card Account object for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/new-credit-card
     * @param CreditCard $creditCardInput
     * @return CreditCard
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function newCreditCard(CreditCard $creditCardInput): CreditCard
    {
        $requestData = $creditCardInput->toArray();
        $response = $this->client->post('account/creditcard', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }

        $apiResponseData = $response['data']; // This should be stdClass
        return CreditCard::fromStdClass($apiResponseData);
    }

    /**
     * Returns the Credit Card object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/credit-card
     * @param int $account_id
     * @return CreditCard
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function getCreditCard(int $account_id): CreditCard
    {
        $response = $this->client->get(sprintf("account/creditcard/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return CreditCard::fromStdClass($apiResponseData);
    }

    /**
     * Updates the ExpirationDate and/or BillingZipCode on the credit card for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-credit-card
     * @param CreditCard $creditCardInput
     * @return CreditCard
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function updateCreditCard(CreditCard $creditCardInput): CreditCard
    {
        $requestData = $creditCardInput->toArray();
        $response = $this->client->put('account/creditcard', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }

        $apiResponseData = $response['data']; // This should be stdClass
        return CreditCard::fromStdClass($apiResponseData);
    }

    /**
     * Deletes the credit card object for the specified account.
     *
     * @see https://documentation.paysimple.com/reference/delete-credit-card
     * @param int $account_id
     * @return bool
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function deleteCreditCard(int $account_id): bool
    {
        $response = $this->client->delete(sprintf("account/creditcard/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return true;
    }



    // --- ACH ---

    /**
     * Creates a new ACH Account object for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/new-ach
     * @param ACHAccount $achAccountInput
     * @return ACHAccount
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function newAch(ACHAccount $achAccountInput): ACHAccount
    {
        $requestData = $achAccountInput->toArray();
        $response = $this->client->post('account/ach', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }

        $apiResponseData = $response['data']; // This should be stdClass
        return ACHAccount::fromStdClass($apiResponseData);
    }

    /**
     * Returns a the ACH Account object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/ach-record
     * @param int $account_id
     * @return ACHAccount
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function getAch(int $account_id): ACHAccount
    {
        $response = $this->client->get(sprintf("account/ach/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return ACHAccount::fromStdClass($apiResponseData);
    }

    /**
     * Updates the IsCheckingAccounton the ACH Account object for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-ach
     * @param ACHAccount $achAccountInput
     * @return ACHAccount
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function updateAch(ACHAccount $achAccountInput): ACHAccount
    {
        $requestData = $achAccountInput->toArray();
        $response = $this->client->put('account/ach', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }

        $apiResponseData = $response['data']; // This should be stdClass
        return ACHAccount::fromStdClass($apiResponseData);
    }

    /**
     * Deletes the ACH (bank) Account object for the specified account.
     *
     * @see https://documentation.paysimple.com/reference/delete-ach
     * @param int $account_id
     * @return bool
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function deleteAch(int $account_id): bool
    {
        $response = $this->client->delete(sprintf("account/ach/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return true;
    }
}
