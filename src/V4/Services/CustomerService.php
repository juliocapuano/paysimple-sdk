<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;

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
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function new(array $customer): object
    {
        $response = $this->client->post('customer', $customer);
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a Customer object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/customer-2
     * @param int $customer_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function get(int $customer_id): object
    {
        $response = $this->client->get(sprintf("customer/%s", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Filterable and sortable list of all customers.
     *
     * @see https://documentation.paysimple.com/reference/list-customers
     * @param array $params
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function list(array $params = []): array
    {
        $response = $this->client->get('customer', $params);
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a list of all payment accounts (both credit card and ACH bank accounts) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function listAccounts(int $customer_id): array
    {
        $response = $this->client->get(sprintf("customer/%s/accounts", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Sets the default ACH or Credit Card for the specified CustomerId and AccountId
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridaccountid
     * @param int $customer_id
     * @param int $account_id
     * @return bool
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function setDefaultAccount(int $customer_id, int $account_id): bool
    {
        $response = $this->client->put(sprintf("customer/%s/%s", $customer_id, $account_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return true;
    }

    /**
     * Returns a list of all ACH (bank) accounts associated with a specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridachaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function achAccounts(int $customer_id): array
    {
        $response = $this->client->get(sprintf("customer/%s/achaccounts", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a list of all Credit Card accounts associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridcreditcardaccounts
     * @param int $customer_id
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function creditCardsAccounts(int $customer_id): array
    {
        $response = $this->client->get(sprintf("customer/%s/creditcardaccounts", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns the default ACH account (bank account) associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeriddefaultach
     * @param int $customer_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function defaultAch(int $customer_id): object
    {
        $response = $this->client->get(sprintf("customer/%s/defaultach", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns the default credit card account associated with the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeriddefaultcreditcard
     * @param int $customer_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function defaultCreditCard(int $customer_id): object
    {
        $response = $this->client->get(sprintf("customer/%s/defaultcreditcard", $customer_id));
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a list of payment records for the specified customer.
     *
     * @see https://documentation.paysimple.com/reference/customercustomeridpayments
     * @param int $customer_id
     * @param array $params
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function listOfPayments(int $customer_id, array $params = []): array
    {
        $response = $this->client->get(sprintf("customer/%s/payments", $customer_id), $params);
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Updates the customer object for the customer specified in the request body.
     *
     * @see https://documentation.paysimple.com/reference/customer-1
     * @param array $customer
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function update(array $customer): object
    {
        $response = $this->client->put('customer', $customer);
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Deletes the Customer object for the specified id.
     *
     * @see https://documentation.paysimple.com/reference/customercustomerid
     * @param int $customer_id
     * @return bool
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function delete(int $customer_id): bool
    {
        $response = $this->client->delete(sprintf("customer/%s", $customer_id)); // Corrected endpoint
        if ($this->client->hasErrors() || !empty($response['error'])) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return true;
    }
}
