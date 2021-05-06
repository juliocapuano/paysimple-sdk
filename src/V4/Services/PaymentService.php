<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

class PaymentService extends Service
{

    /**
     * Collect a payment for the specified AccountId.
     *
     * @see https://documentation.paysimple.com/reference#new-payment
     * @param array $payment
     * @return array
     * @throws GuzzleException
     */
    public function new(array $payment)
    {
        return $this->client->post('payment', $payment);
    }

    /**
     * Returns a payment object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference#payment
     * @param $payment_id
     * @return array
     * @throws GuzzleException
     */
    public function get(int $payment_id)
    {
        return $this->client->get("payment/{$payment_id}");
    }

    /**
     * Filterable and sortable list of all payment records.
     *
     * @see https://documentation.paysimple.com/reference#payments
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function list(array $params = [])
    {
        return $this->client->get('payment', $params);
    }

    /**
     * Refunds a settled payment of the specified Id.
     *
     * @see https://documentation.paysimple.com/reference#reverse-payment
     * @param int $payment_id
     * @return array
     * @throws GuzzleException
     */
    public function refund(int $payment_id)
    {
        return $this->client->put("payment/{$payment_id}/reverse");
    }
}
