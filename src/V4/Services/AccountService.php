<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

class AccountService extends Service
{

    // --- Credit Card ---
    /**
     * Creates a new Credit Card Account object for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/new-credit-card
     * @param array $credit_card
     * @return array
     * @throws GuzzleException
     */
    final public function newCreditCard(array $credit_card): array
    {
        return $this->client->post('account/creditcard', $credit_card);
    }

    /**
     * Returns the Credit Card object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/credit-card
     * @param int $account_id
     * @return array
     * @throws GuzzleException
     */
    final public function getCreditCard(int $account_id): array
    {
        return $this->client->get(sprintf("account/creditcard/%s", $account_id));
    }

    /**
     * Updates the ExpirationDate and/or BillingZipCode on the credit card for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-credit-card
     * @param array $credit_card
     * @return array
     * @throws GuzzleException
     */
    final public function updateCreditCard(array $credit_card): array
    {
        return $this->client->put('account/creditcard', $credit_card);
    }

    /**
     * Deletes the credit card object for the specified account.
     *
     * @see https://documentation.paysimple.com/reference/delete-credit-card
     * @param int $account_id
     * @return array
     * @throws GuzzleException
     */
    final public function deleteCreditCard(int $account_id): array
    {
        return $this->client->delete(sprintf("account/creditcard/%s", $account_id));
    }



    // --- ACH ---

    /**
     * Creates a new ACH Account object for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/new-ach
     * @param array $ach
     * @return array
     * @throws GuzzleException
     */
    final public function newAch(array $ach): array
    {
        return $this->client->post('account/ach', $ach);
    }

    /**
     * Returns a the ACH Account object for the specified account id.
     *
     * @see https://documentation.paysimple.com/reference/ach-record
     * @param int $account_id
     * @return array
     * @throws GuzzleException
     */
    final public function getAch(int $account_id): array
    {
        return $this->client->get(sprintf("account/ach/%s", $account_id));
    }

    /**
     * Updates the IsCheckingAccounton the ACH Account object for the account specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/update-ach
     * @param array $ach
     * @return array
     * @throws GuzzleException
     */
    final public function updateAch(array $ach): array
    {
        return $this->client->put('account/ach', $ach);
    }

    /**
     * Deletes the ACH (bank) Account object for the specified account.
     *
     * @see https://documentation.paysimple.com/reference/delete-ach
     * @param int $account_id
     * @return array
     * @throws GuzzleException
     */
    final public function deleteAch(int $account_id): array
    {
        return $this->client->delete(sprintf("account/ach/%s", $account_id));
    }
}
