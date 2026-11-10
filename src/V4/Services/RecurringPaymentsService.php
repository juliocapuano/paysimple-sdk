<?php

namespace PaySimple\V4\Services;

use PaySimple\V4\Domain\Entity\RecurringPayment;

class RecurringPaymentsService extends Service
{
    final public function new(array|RecurringPayment $recurringPayment): array
    {
        $data = $recurringPayment instanceof RecurringPayment ? $recurringPayment->toArray() : $recurringPayment;
        return $this->client->post('recurringpayment', $data);
    }

    final public function get(int $scheduleId): RecurringPayment
    {
        $response = $this->client->get(sprintf('recurringpayment/%s', $scheduleId));
        return RecurringPayment::fromArray($response['data']);
    }

    final public function list(array $filters = []): array
    {
        return $this->client->get('recurringpayment', $filters);
    }

    final public function customerList(int $customerId): array
    {
        return $this->client->get(sprintf('customer/%s/recurringpayments', $customerId));
    }

    final public function paymentList(int $scheduleId): array
    {
        return $this->client->get(sprintf('recurringpayment/%s/payments', $scheduleId));
    }

    final public function update(array|RecurringPayment $recurringPayment): array
    {
        $data = $recurringPayment instanceof RecurringPayment ? $recurringPayment->toArray() : $recurringPayment;
        return $this->client->put('recurringpayment', $data);
    }

    final public function suspend(int $scheduleId): array
    {
        return $this->client->put(sprintf('recurringpayment/%s/suspend', $scheduleId));
    }

    final public function pause(int $scheduleId, \DateTime $endDate): array
    {
        $finalEndDate = $endDate->format('Y-m-d');
        return $this->client->put(sprintf('recurringpayment/%s/pause?enddate=%s', $scheduleId, $finalEndDate));
    }

    final public function resume(int $scheduleId): array
    {
        return $this->client->put(sprintf('recurringpayment/%s/resume', $scheduleId));
    }

    final public function delete(int $scheduleId): array
    {
        return $this->client->delete(sprintf('recurringpayment/%s', $scheduleId));
    }
}
