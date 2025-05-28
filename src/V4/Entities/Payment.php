<?php

namespace PaySimple\V4\Entities;

use stdClass;

class Payment
{
    // Response fields & General Properties
    public ?int $Id = null;
    public ?string $Status = null;
    public ?string $ProviderAuthCode = null;
    public ?string $TraceNumber = null;
    public ?object $FailureData = null; // Can be stdClass { Code, Message }
    public ?string $CustomerFirstName = null;
    public ?string $CustomerLastName = null;
    public ?int $RecurringScheduleId = null;
    public ?string $PaymentType = null;      // e.g., "CC", "ACH"
    public ?string $PaymentSubType = null;   // e.g., "MOTO", "WEB", "PPD", "CCD"
    public ?string $PaymentDate = null;
    public ?string $ReturnDate = null;
    public ?string $EstimatedSettleDate = null;
    public ?string $ActualSettledDate = null;
    public ?string $CanVoidUntil = null;
    public ?string $LastModified = null;
    public ?string $CreatedOn = null;

    // Request & Response fields (often required for request)
    public ?int $CustomerId = null; // Optional for request if AccountId is from a vaulted account that has CustomerId
    public ?int $AccountId = null;  // Required for request
    public ?float $Amount = null;   // Required for request
    public ?bool $IsDebit = false;  // Default to false (payment). True for refund.
    public ?string $InvoiceId = null;
    public ?string $InvoiceNumber = null;
    public ?string $PurchaseOrderNumber = null;
    public ?string $OrderId = null;
    public ?string $Description = null;
    public ?int $ReferenceId = null; // Can be request and response

    // Request only fields
    public ?object $SuccessReceiptOptions = null; // stdClass { SendToCustomer, SendToOtherAddresses[] }
    public ?object $FailureReceiptOptions = null; // stdClass { SendToCustomer, SendToOtherAddresses[] }
    public ?string $CVV = null; // For request only, not stored

    public static function fromStdClass(stdClass $data): self
    {
        $payment = new self();

        $payment->Id = $data->Id ?? null;
        $payment->Status = $data->Status ?? null;
        $payment->ProviderAuthCode = $data->ProviderAuthCode ?? null;
        $payment->TraceNumber = $data->TraceNumber ?? null;
        if (isset($data->FailureData) && is_object($data->FailureData)) {
            $payment->FailureData = $data->FailureData;
        }
        $payment->CustomerId = $data->CustomerId ?? null;
        $payment->CustomerFirstName = $data->CustomerFirstName ?? null;
        $payment->CustomerLastName = $data->CustomerLastName ?? null;
        $payment->ReferenceId = $data->ReferenceId ?? null;
        $payment->RecurringScheduleId = $data->RecurringScheduleId ?? null;
        $payment->PaymentType = $data->PaymentType ?? null;
        $payment->PaymentSubType = $data->PaymentSubType ?? null;
        $payment->PaymentDate = $data->PaymentDate ?? null;
        $payment->ReturnDate = $data->ReturnDate ?? null;
        $payment->EstimatedSettleDate = $data->EstimatedSettleDate ?? null;
        $payment->ActualSettledDate = $data->ActualSettledDate ?? null;
        $payment->CanVoidUntil = $data->CanVoidUntil ?? null;
        $payment->AccountId = $data->AccountId ?? null; // Also in response
        $payment->Amount = isset($data->Amount) ? (float)$data->Amount : null; // Also in response
        $payment->IsDebit = $data->IsDebit ?? false; // Also in response
        $payment->InvoiceId = $data->InvoiceId ?? null; // Also in response
        $payment->InvoiceNumber = $data->InvoiceNumber ?? null;
        $payment->PurchaseOrderNumber = $data->PurchaseOrderNumber ?? null;
        $payment->OrderId = $data->OrderId ?? null;
        $payment->Description = $data->Description ?? null; // Also in response
        $payment->LastModified = $data->LastModified ?? null;
        $payment->CreatedOn = $data->CreatedOn ?? null;

        // SuccessReceiptOptions and FailureReceiptOptions are not typically in responses
        // CVV is not in responses

        return $payment;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->AccountId !== null) {
            $array['AccountId'] = $this->AccountId;
        }
        if ($this->Amount !== null) {
            $array['Amount'] = $this->Amount;
        }
        if ($this->CVV !== null) { // Only for CC payments, not stored
            $array['CVV'] = $this->CVV;
        }
        if ($this->PaymentSubType !== null) { // Required for ACH
            $array['PaymentSubType'] = $this->PaymentSubType;
        }
        if ($this->IsDebit !== null) { // Useful for refunds
            $array['IsDebit'] = $this->IsDebit;
        }

        // Optional fields for new payment
        if ($this->CustomerId !== null) { // Optional if AccountId implies CustomerId
            $array['CustomerId'] = $this->CustomerId;
        }
        if ($this->ReferenceId !== null) {
            $array['ReferenceId'] = $this->ReferenceId;
        }
        if ($this->InvoiceId !== null) {
            $array['InvoiceId'] = $this->InvoiceId;
        }
        if ($this->InvoiceNumber !== null) {
            $array['InvoiceNumber'] = $this->InvoiceNumber;
        }
        if ($this->PurchaseOrderNumber !== null) {
            $array['PurchaseOrderNumber'] = $this->PurchaseOrderNumber;
        }
        if ($this->OrderId !== null) {
            $array['OrderId'] = $this->OrderId;
        }
        if ($this->Description !== null) {
            $array['Description'] = $this->Description;
        }
        if ($this->SuccessReceiptOptions !== null) {
            $array['SuccessReceiptOptions'] = (array)$this->SuccessReceiptOptions;
        }
        if ($this->FailureReceiptOptions !== null) {
            $array['FailureReceiptOptions'] = (array)$this->FailureReceiptOptions;
        }
        
        // Read-only fields like Id, Status, ProviderAuthCode, TraceNumber, FailureData,
        // CustomerFirstName, CustomerLastName, RecurringScheduleId, PaymentType, PaymentDate,
        // ReturnDate, EstimatedSettleDate, ActualSettledDate, CanVoidUntil,
        // LastModified, CreatedOn are excluded.

        return $array;
    }
}
