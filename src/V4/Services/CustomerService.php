<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\Customer;

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
     * @param Customer $customerInput
     * @return Customer
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function new(Customer $customerInput): Customer
    {
        $requestData = $customerInput->toArray();
        $response = $this->client->post('customer', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        
        $apiResponseData = $response['data']; // This should be stdClass
        return Customer::fromStdClass($apiResponseData);
    }

    /**
     * Returns a Customer object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/customer-2
     * @param int $customer_id
     * @return Customer
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function get(int $customer_id): Customer
    {
        $response = $this->client->get(sprintf("customer/%s", $customer_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return Customer::fromStdClass($apiResponseData);
    }

    /**
     * Filterable and sortable list of all customers.
     *
     * @see https://documentation.paysimple.com/reference/list-customers
     * @param array $params
     * @return Customer[]
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function list(array $params = []): array
    {
        $response = $this->client->get('customer', $params);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        
        $apiResponseDataArray = $response['data']; // This is an array of stdClass objects
        $customers = [];
        if (is_array($apiResponseDataArray)) {
            foreach ($apiResponseDataArray as $customerData) {
                if ($customerData instanceof \stdClass) {
                    $customers[] = Customer::fromStdClass($customerData);
                }
                // Optionally, handle cases where $customerData is not an stdClass,
                // though the API client should consistently return this structure.
            }
        }
        return $customers;
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
     * @return array
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
     * @param Customer $customerInput
     * @return Customer
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function update(Customer $customerInput): Customer
    {
        $requestData = $customerInput->toArray();
        // The 'Id' from $customerInput might be in $requestData.
        // PaySimple's Update Customer API usually expects the ID in the URL, not the body.
        // However, their docs for "Update Customer" show the Id in the body.
        // We'll assume the toArray() method of Customer entity handles this correctly for now.
        // If 'Id' should NOT be in the body, Customer::toArray() should be adjusted,
        // or we unset($requestData['Id']) here if it's always present and not desired.
        
        $response = $this->client->put('customer', $requestData);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return Customer::fromStdClass($apiResponseData);
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
