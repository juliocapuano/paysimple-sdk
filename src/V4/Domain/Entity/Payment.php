<?php

namespace PaySimple\V4\Domain\Entity;

use PaySimple\V4\Domain\ValueObject\Money;

class Payment
{
    private ?int $paymentId = null;
    private ?int $customerId = null;
    private ?Money $amount = null;
    private ?string $transactionDate = null;
    private ?string $transactionType = null;
    private ?int $accountId = null;
    private ?string $invoiceNumber = null;
    private ?string $comment = null;
    private bool $isRecurring = false;
    private ?int $scheduleId = null;

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(int $paymentId): self
    {
        $this->paymentId = $paymentId;
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

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;
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

    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): self
    {
        $this->isRecurring = $isRecurring;
        return $this;
    }

    public function getScheduleId(): ?int
    {
        return $this->scheduleId;
    }

    public function setScheduleId(int $scheduleId): self
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->paymentId !== null) {
            $data['PaymentId'] = $this->paymentId;
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

        if ($this->transactionType !== null) {
            $data['TransactionType'] = $this->transactionType;
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

        if ($this->isRecurring) {
            $data['IsRecurring'] = $this->isRecurring;
        }

        if ($this->scheduleId !== null) {
            $data['ScheduleId'] = $this->scheduleId;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        $payment = new self();

        if (isset($data['PaymentId'])) {
            $payment->setPaymentId($data['PaymentId']);
        }

        if (isset($data['CustomerId'])) {
            $payment->setCustomerId($data['CustomerId']);
        }

        if (isset($data['Amount'])) {
            $payment->setAmount($data['Amount']);
        }

        if (isset($data['TransactionDate'])) {
            $payment->setTransactionDate($data['TransactionDate']);
        }

        if (isset($data['TransactionType'])) {
            $payment->setTransactionType($data['TransactionType']);
        }

        if (isset($data['AccountId'])) {
            $payment->setAccountId($data['AccountId']);
        }

        if (isset($data['InvoiceNumber'])) {
            $payment->setInvoiceNumber($data['InvoiceNumber']);
        }

        if (isset($data['Comment'])) {
            $payment->setComment($data['Comment']);
        }

        if (isset($data['IsRecurring'])) {
            $payment->setIsRecurring($data['IsRecurring']);
        }

        if (isset($data['ScheduleId'])) {
            $payment->setScheduleId($data['ScheduleId']);
        }

        return $payment;
    }
}
