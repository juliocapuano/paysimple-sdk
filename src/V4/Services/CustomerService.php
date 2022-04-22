<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Class CustomerService
 * @package PaySimple\V4\Services
 * @see https://documentation.paysimple.com/reference/customer-object
 */
class CustomerService extends Service
{
    /**
     * Creates a new customer object
     *
     * @see https://documentation.paysimple.com/reference/new-customer
     * @param array $customer
     * @return array
     * @throws GuzzleException
     */
    final public function new(array $customer): array
    {
        return $this->client->post('customer', $customer);
    }

    /**
     * Returns a Customer object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/customer-2
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function get(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s", $customer_id));
    }

    /**
     * Filterable and sortable list of all customers.
     *
     * @see https://documentation.paysimple.com/reference/list-customers
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    final public function list(array $params = []): array
    {
        return $this->client->get('customer', $params);
    }

    /**
     * Returns a list of all payment accounts (both credit card and ACH bank accounts) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function listAccounts(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s/accounts", $customer_id));
    }

    /**
     * Sets the default ACH or Credit Card for the specified CustomerId and AccountId
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridaccountid
     * @param int $customer_id
     * @param int $account_id
     * @return mixed
     * @throws GuzzleException
     */
    final public function setDefaultAccount(int $customer_id, int $account_id): array
    {
        return $this->client->put(sprintf("customer/%s/%s", $customer_id, $account_id));
    }

    /**
     * Returns a list of all ACH (bank) accounts associated with a specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridachaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function achAccounts(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s/achaccounts", $customer_id));
    }

    /**
     * Returns a list of all Credit Card accounts associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridcreditcardaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function creditCardsAccounts(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s/creditcardaccounts", $customer_id));
    }

    /**
     * Returns the default ACH account (bank account) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeriddefaultach
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function defaultAch(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s/defaultach", $customer_id));
    }

    /**
     * Returns the default credit card account associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeriddefaultcreditcard
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function defaultCreditCard(int $customer_id): array
    {
        return $this->client->get(sprintf("customer/%s/defaultcreditcard", $customer_id));
    }

    /**
     * Returns a list of payment records for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridpayments
     * @param int $customer_id
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    final public function listOfPayments(int $customer_id, array $params = []): array
    {
        return $this->client->get(sprintf("customer/%s/payments", $customer_id), $params);
    }

    /**
     * Updates the customer object for the customer specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/customer-1
     * @param array $customer
     * @return array
     * @throws GuzzleException
     */
    final public function update(array $customer): array
    {
        return $this->client->put('customer', $customer);
    }

    /**
     * Deletes the Customer object for the specified id.
     *
     * @see https://documentation.paysimple.com/reference/customercustomerid
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    final public function delete(int $customer_id): array
    {
        return $this->client->delete(sprintf("customer/%s/payments", $customer_id));
    }
}
