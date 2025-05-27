<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;

class PaymentService extends Service
{

    /**
     * Collect a payment for the specified AccountId.
     *
     * @see https://documentation.paysimple.com/reference/new-payment
     * @param array $payment
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function new(array $payment): object
    {
        $response = $this->client->post('payment', $payment);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Returns a payment object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/payment
     * @param int $payment_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function get(int $payment_id): object
    {
        $response = $this->client->get(sprintf("payment/%s", $payment_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Filterable and sortable list of all payment records.
     *
     * @see https://documentation.paysimple.com/reference/payments
     * @param array $params
     * @return array
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function list(array $params = []): array
    {
        $response = $this->client->get('payment', $params);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Refunds a settled payment of the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/reverse-payment
     * @param int $payment_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function refund(int $payment_id): object
    {
        $response = $this->client->put(sprintf("payment/%s/reverse", $payment_id), []);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }

    /**
     * Voids the payment of the specified id.
     *
     * @see https://documentation.paysimple.com/reference/void-payment
     * @param int $payment_id
     * @return object
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function void(int $payment_id): object
    {
        $response = $this->client->put(sprintf("payment/%s/void", $payment_id), []);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        return $response['data'];
    }
}
