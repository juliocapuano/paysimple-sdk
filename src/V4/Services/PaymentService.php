<?php

namespace PaySimple\V4\Services;

use PaySimple\V4\Domain\Entity\Payment;

class PaymentService extends Service
{
    final public function new(array|Payment $payment): array
    {
        $data = $payment instanceof Payment ? $payment->toArray() : $payment;
        return $this->client->post('payment', $data);
    }

    final public function get(int $paymentId): Payment
    {
        $response = $this->client->get(sprintf('payment/%s', $paymentId));
        return Payment::fromArray($response['data']);
    }

    final public function list(array $params = []): array
    {
        return $this->client->get('payment', $params);
    }

    final public function refund(int $paymentId): array
    {
        return $this->client->put(sprintf('payment/%s/reverse', $paymentId));
    }

    final public function void(int $paymentId): array
    {
        return $this->client->put(sprintf('payment/%s/void', $paymentId));
    }
}
