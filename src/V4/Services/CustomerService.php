<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

class CustomerService extends Service
{
    /**
     * Creates a new customer object
     *
     * @see https://documentation.paysimple.com/reference#new-customer
     * @param array $customer
     * @return array
     * @throws GuzzleException
     */
    public function new(array $customer)
    {
        return $this->client->post('customer', $customer);
    }

    /**
     * Returns a Customer object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference#customer-2
     * @param $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function get(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}");
    }

    /**
     * Filterable and sortable list of all customers.
     *
     * @see https://documentation.paysimple.com/reference#list-customers
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function list(array $params = [])
    {
        return $this->client->get('customer', $params);
    }

    /**
     * Returns a list of all payment accounts (both credit card and ACH bank accounts) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeridaccounts
     * @param $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function listAccounts(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}/accounts");
    }

    /**
     * Sets the default ACH or Credit Card for the specified CustomerId and AccountId
     *
     * @see https://documentation.paysimple.com/reference#customercustomeridaccountid
     * @param int $customer_id
     * @param int $account_id
     * @return mixed
     * @throws GuzzleException
     */
    public function setDefaultAccount(int $customer_id, int $account_id)
    {
        return $this->client->put("customer/{$customer_id}/{$account_id}");
    }

    /**
     * Returns a list of all ACH (bank) accounts associated with a specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeridachaccounts
     * @param $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function achAccounts(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}/achaccounts");
    }

    /**
     * Returns a list of all Credit Card accounts associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeridcreditcardaccounts
     * @param $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function creditCardsAccounts(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}/creditcardaccounts");
    }

    /**
     * Returns the default ACH account (bank account) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeriddefaultach
     * @param $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function defaultAch(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}/defaultach");
    }

    /**
     * Returns the default credit card account associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeriddefaultcreditcard
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function defaultCreditCard(int $customer_id)
    {
        return $this->client->get("customer/{$customer_id}/defaultcreditcard");
    }

    /**
     * Returns a list of payment records for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference#customercustomeridpayments
     * @param int $customer_id
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function listOfPayments(int $customer_id, array $params = [])
    {
        return $this->client->get("customer/{$customer_id}/payments", $params);
    }

    /**
     * Updates the customer object for the customer specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference#customer-1
     * @param array $customer
     * @return array
     * @throws GuzzleException
     */
    public function update(array $customer)
    {
        return $this->client->put('customer', $customer);
    }

    /**
     * Deletes the Customer object for the specified id.
     *
     * @see https://documentation.paysimple.com/reference#customercustomerid
     * @param int $customer_id
     * @return array
     * @throws GuzzleException
     */
    public function delete(int $customer_id)
    {
        return $this->client->delete("customer/{$customer_id}/payments");
    }
}
