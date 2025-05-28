<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\Payment;

class PaymentService extends Service
{

    /**
     * Collect a payment for the specified AccountId.
     *
     * @see https://documentation.paysimple.com/reference/new-payment
     * @param Payment $paymentInput
     * @return Payment
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function new(Payment $paymentInput): Payment
    {
        $requestData = $paymentInput->toArray();
        $response = $this->client->post('payment', $requestData);

        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        
        $apiResponseData = $response['data']; // This should be stdClass
        return Payment::fromStdClass($apiResponseData);
    }

    /**
     * Returns a payment object for the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/payment
     * @param int $payment_id
     * @return Payment
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function get(int $payment_id): Payment
    {
        $response = $this->client->get(sprintf("payment/%s", $payment_id));
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return Payment::fromStdClass($apiResponseData);
    }

    /**
     * Filterable and sortable list of all payment records.
     *
     * @see https://documentation.paysimple.com/reference/payments
     * @param array $params
     * @return Payment[]
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function list(array $params = []): array
    {
        $response = $this->client->get('payment', $params);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        
        $apiResponseDataArray = $response['data']; // This is an array of stdClass objects
        $payments = [];
        if (is_array($apiResponseDataArray)) {
            foreach ($apiResponseDataArray as $paymentData) {
                if ($paymentData instanceof \stdClass) { // Ensure it's an object before passing
                    $payments[] = Payment::fromStdClass($paymentData);
                }
            }
        }
        return $payments;
    }

    /**
     * Refunds a settled payment of the specified Id.
     *
     * @see https://documentation.paysimple.com/reference/reverse-payment
     * @param int $payment_id
     * @return Payment
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function refund(int $payment_id): Payment
    {
        $response = $this->client->put(sprintf("payment/%s/reverse", $payment_id), []);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return Payment::fromStdClass($apiResponseData);
    }

    /**
     * Voids the payment of the specified id.
     *
     * @see https://documentation.paysimple.com/reference/void-payment
     * @param int $payment_id
     * @return Payment
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function void(int $payment_id): Payment
    {
        $response = $this->client->put(sprintf("payment/%s/void", $payment_id), []);
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return Payment::fromStdClass($apiResponseData);
    }
}
