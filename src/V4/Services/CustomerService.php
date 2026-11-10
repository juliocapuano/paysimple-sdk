<?php

namespace PaySimple\V4\Services;

use PaySimple\V4\Domain\Entity\Customer;

class CustomerService extends Service
{
    final public function new(array|Customer $customer): array
    {
        $data = $customer instanceof Customer ? $customer->toArray() : $customer;
        return $this->client->post('customer', $data);
    }

    final public function get(int $customerId): Customer
    {
        $response = $this->client->get(sprintf('customer/%s', $customerId));
        return Customer::fromArray($response['data']);
    }

    final public function list(array $params = []): array
    {
        return $this->client->get('customer', $params);
    }

    final public function listAccounts(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/accounts', $customerId));
    }

    final public function setDefaultAccount(int $customerId, int $accountId): array
    {
        return $this->client->put(sprintf('customer/%s/%s', $customerId, $accountId));
    }

    final public function achAccounts(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/achaccounts', $customerId));
    }

    final public function creditCardsAccounts(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/creditcardaccounts', $customerId));
    }

    final public function defaultAch(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/defaultach', $customerId));
    }

    final public function defaultCreditCard(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/defaultcreditcard', $customerId));
    }

    final public function listOfPayments(int $customerId, array $params = []): array
    {
        return $this->client->get(sprintf('customer/%s/payments', $customerId), $params);
    }

    final public function update(array|Customer $customer): array
    {
        $data = $customer instanceof Customer ? $customer->toArray() : $customer;
        return $this->client->put('customer', $data);
    }

    final public function delete(int $customerId): array
    {
        return $this->client->delete(sprintf('customer/%s', $customerId));
    }
}
