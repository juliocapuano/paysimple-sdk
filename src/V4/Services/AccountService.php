<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;

class AccountService extends Service
{

    // --- Credit Card ---
    /**
     * Creates a new Credit Card Account object for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/new-credit-card
     * @param array $credit_card
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function newCreditCard(array $credit_card): object
    {
        $response = $this->client->post('account/creditcard', $credit_card);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns the Credit Card object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/credit-card
     * @param int $account_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function getCreditCard(int $account_id): object
    {
        $response = $this->client->get(sprintf("account/creditcard/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Updates the ExpirationDate and/or BillingZipCode on the credit card for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-credit-card
     * @param array $credit_card
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function updateCreditCard(array $credit_card): object
    {
        $response = $this->client->put('account/creditcard', $credit_card);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
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
     * @param array $ach
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function newAch(array $ach): object
    {
        $response = $this->client->post('account/ach', $ach);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a the ACH Account object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/ach-record
     * @param int $account_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function getAch(int $account_id): object
    {
        $response = $this->client->get(sprintf("account/ach/%s", $account_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Updates the IsCheckingAccounton the ACH Account object for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-ach
     * @param array $ach
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function updateAch(array $ach): object
    {
        $response = $this->client->put('account/ach', $ach);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
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
