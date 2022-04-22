<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

class PaymentService extends Service
{

    /**
     * Collect a payment for the specified AccountId.
     *
     * @see https://documentation.paysimple.com/reference/new-payment
     * @param array $payment
     * @return array
     * @throws GuzzleException
     */
    final public function new(array $payment): array
    {
        return $this->client->post('payment', $payment);
    }

    /**
     * Returns a payment object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/payment
     * @param int $payment_id
     * @return array
     * @throws GuzzleException
     */
    final public function get(int $payment_id): array
    {
        return $this->client->get(sprintf("payment/%s", $payment_id));
    }

    /**
     * Filterable and sortable list of all payment records.
     *
     * @see https://documentation.paysimple.com/reference/payments
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    final public function list(array $params = []): array
    {
        return $this->client->get('payment', $params);
    }

    /**
     * Refunds a settled payment of the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/reverse-payment
     * @param int $payment_id
     * @return array
     * @throws GuzzleException
     */
    final public function refund(int $payment_id): array
    {
        return $this->client->put(sprintf("payment/%s/reverse", $payment_id));
    }

    /**
     * Voids the payment of the specified id.
     *
     * @see https://documentation.paysimple.com/reference/void-payment
     * @param int $payment_id
     * @return array
     * @throws GuzzleException
     */
    final public function void(int $payment_id): array
    {
        return $this->client->put(sprintf("payment/%s/void", $payment_id));
    }
}
