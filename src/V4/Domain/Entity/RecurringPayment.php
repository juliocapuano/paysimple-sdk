<?php

namespace PaySimple\V4\Domain\Entity;

use PaySimple\V4\Domain\ValueObject\Money;

class RecurringPayment
{
    private ?int $scheduleId = null;
    private ?int $customerId = null;
    private ?Money $amount = null;
    private ?string $transactionDate = null;
    private ?string $nextTransactionDate = null;
    private ?string $frequency = null;
    private ?int $numberOfPayments = null;
    private ?int $numberOfPaymentsProcessed = null;
    private bool $isActive = false;
    private ?int $accountId = null;
    private ?string $invoiceNumber = null;
    private ?string $comment = null;

    public function getScheduleId(): ?int
    {
        return $this->scheduleId;
    }

    public function setScheduleId(int $scheduleId): self
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getAmount(): ?Money
    {
        return $this->amount;
    }

    public function setAmount(float|Money $amount): self
    {
        if (is_float($amount) || is_int($amount)) {
            $this->amount = Money::fromDollars((float) $amount);
        } else {
            $this->amount = $amount;
        }
        return $this;
    }

    public function getTransactionDate(): ?string
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(string $transactionDate): self
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    public function getNextTransactionDate(): ?string
    {
        return $this->nextTransactionDate;
    }

    public function setNextTransactionDate(string $nextTransactionDate): self
    {
        $this->nextTransactionDate = $nextTransactionDate;
        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = $frequency;
        return $this;
    }

    public function getNumberOfPayments(): ?int
    {
        return $this->numberOfPayments;
    }

    public function setNumberOfPayments(int $numberOfPayments): self
    {
        $this->numberOfPayments = $numberOfPayments;
        return $this;
    }

    public function getNumberOfPaymentsProcessed(): ?int
    {
        return $this->numberOfPaymentsProcessed;
    }

    public function setNumberOfPaymentsProcessed(int $numberOfPaymentsProcessed): self
    {
        $this->numberOfPaymentsProcessed = $numberOfPaymentsProcessed;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->scheduleId !== null) {
            $data['ScheduleId'] = $this->scheduleId;
        }

        if ($this->customerId !== null) {
            $data['CustomerId'] = $this->customerId;
        }

        if ($this->amount !== null) {
            $data['Amount'] = $this->amount->toDollars();
        }

        if ($this->transactionDate !== null) {
            $data['TransactionDate'] = $this->transactionDate;
        }

        if ($this->nextTransactionDate !== null) {
            $data['NextTransactionDate'] = $this->nextTransactionDate;
        }

        if ($this->frequency !== null) {
            $data['Frequency'] = $this->frequency;
        }

        if ($this->numberOfPayments !== null) {
            $data['NumberOfPayments'] = $this->numberOfPayments;
        }

        if ($this->numberOfPaymentsProcessed !== null) {
            $data['NumberOfPaymentsProcessed'] = $this->numberOfPaymentsProcessed;
        }

        if ($this->isActive) {
            $data['IsActive'] = $this->isActive;
        }

        if ($this->accountId !== null) {
            $data['AccountId'] = $this->accountId;
        }

        if ($this->invoiceNumber !== null) {
            $data['InvoiceNumber'] = $this->invoiceNumber;
        }

        if ($this->comment !== null) {
            $data['Comment'] = $this->comment;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        $recurringPayment = new self();

        if (isset($data['ScheduleId'])) {
            $recurringPayment->setScheduleId($data['ScheduleId']);
        }

        if (isset($data['CustomerId'])) {
            $recurringPayment->setCustomerId($data['CustomerId']);
        }

        if (isset($data['Amount'])) {
            $recurringPayment->setAmount($data['Amount']);
        }

        if (isset($data['TransactionDate'])) {
            $recurringPayment->setTransactionDate($data['TransactionDate']);
        }

        if (isset($data['NextTransactionDate'])) {
            $recurringPayment->setNextTransactionDate($data['NextTransactionDate']);
        }

        if (isset($data['Frequency'])) {
            $recurringPayment->setFrequency($data['Frequency']);
        }

        if (isset($data['NumberOfPayments'])) {
            $recurringPayment->setNumberOfPayments($data['NumberOfPayments']);
        }

        if (isset($data['NumberOfPaymentsProcessed'])) {
            $recurringPayment->setNumberOfPaymentsProcessed($data['NumberOfPaymentsProcessed']);
        }

        if (isset($data['IsActive'])) {
            $recurringPayment->setIsActive($data['IsActive']);
        }

        if (isset($data['AccountId'])) {
            $recurringPayment->setAccountId($data['AccountId']);
        }

        if (isset($data['InvoiceNumber'])) {
            $recurringPayment->setInvoiceNumber($data['InvoiceNumber']);
        }

        if (isset($data['Comment'])) {
            $recurringPayment->setComment($data['Comment']);
        }

        return $recurringPayment;
    }
}
