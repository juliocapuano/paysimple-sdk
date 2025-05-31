<?php

namespace PaySimple\V4\Entities;

use stdClass;

class RecurringPaymentSchedule
{
    // Response fields & General Properties
    public ?int $Id = null;
    public ?string $ScheduleStatus = null;
    public ?string $NextScheduleDate = null;
    public ?string $PauseUntilDate = null;
    public ?bool $FirstPaymentDone = null;
    public ?string $DateOfLastPaymentMade = null;
    public ?float $TotalAmountPaid = null;
    public ?int $NumberOfPaymentsMade = null;
    public ?string $LastModified = null;
    public ?string $CreatedOn = null;

    // Request & Response fields (often required for request)
    public ?int $CustomerId = null;
    public ?int $AccountId = null;
    public ?string $PaymentType = null; // e.g., "CC", "ACH"
    public ?string $PaymentSubType = null; // e.g., "WEB", "PPD"
    public ?float $PaymentAmount = null;
    public ?float $FirstPaymentAmount = null; // Optional
    public ?string $StartDate = null; // YYYY-MM-DD
    public ?string $EndDate = null; // YYYY-MM-DD, optional
    public ?string $ExecutionFrequencyType = null; // e.g., Monthly, Annually, FirstOfMonth
    public ?int $ExecutionFrequencyParameter = null;
    public ?string $Description = null;

    // For convenience, directly on the entity, to be mapped to/from nested options
    public ?bool $SendToCustomerOnSuccess = null;
    public ?array $SendToOtherAddressesOnSuccess = null; // array of strings
    public ?bool $SendToCustomerOnFailure = null;
    public ?array $SendToOtherAddressesOnFailure = null; // array of strings

    // Raw request objects for receipt options (used by toArray)
    // And can be populated by fromStdClass if API returns them nested
    public ?object $SuccessReceiptOptions = null;
    public ?object $FailureReceiptOptions = null;


    public static function fromStdClass(stdClass $data): self
    {
        $schedule = new self();

        $schedule->Id = $data->Id ?? null;
        $schedule->CustomerId = $data->CustomerId ?? null;
        $schedule->AccountId = $data->AccountId ?? null;
        $schedule->PaymentType = $data->PaymentType ?? null;
        $schedule->PaymentSubType = $data->PaymentSubType ?? null;
        $schedule->PaymentAmount = isset($data->PaymentAmount) ? (float)$data->PaymentAmount : null;
        $schedule->FirstPaymentAmount = isset($data->FirstPaymentAmount) ? (float)$data->FirstPaymentAmount : null;
        $schedule->StartDate = $data->StartDate ?? null;
        $schedule->EndDate = $data->EndDate ?? null;
        $schedule->ExecutionFrequencyType = $data->ExecutionFrequencyType ?? null;
        $schedule->ExecutionFrequencyParameter = $data->ExecutionFrequencyParameter ?? null;
        $schedule->ScheduleStatus = $data->ScheduleStatus ?? null;
        $schedule->NextScheduleDate = $data->NextScheduleDate ?? null;
        $schedule->PauseUntilDate = $data->PauseUntilDate ?? null;
        $schedule->Description = $data->Description ?? null;

        $schedule->FirstPaymentDone = $data->FirstPaymentDone ?? null;
        $schedule->DateOfLastPaymentMade = $data->DateOfLastPaymentMade ?? null;
        $schedule->TotalAmountPaid = isset($data->TotalAmountPaid) ? (float)$data->TotalAmountPaid : null;
        $schedule->NumberOfPaymentsMade = $data->NumberOfPaymentsMade ?? null;
        $schedule->LastModified = $data->LastModified ?? null;
        $schedule->CreatedOn = $data->CreatedOn ?? null;

        // Handle receipt options (API typically returns them nested if set)
        if (isset($data->SuccessReceiptOptions) && is_object($data->SuccessReceiptOptions)) {
            $schedule->SuccessReceiptOptions = $data->SuccessReceiptOptions;
            $schedule->SendToCustomerOnSuccess = $data->SuccessReceiptOptions->SendToCustomer ?? null;
            $schedule->SendToOtherAddressesOnSuccess = $data->SuccessReceiptOptions->SendToOtherAddresses ?? null;
        }
        if (isset($data->FailureReceiptOptions) && is_object($data->FailureReceiptOptions)) {
            $schedule->FailureReceiptOptions = $data->FailureReceiptOptions;
            $schedule->SendToCustomerOnFailure = $data->FailureReceiptOptions->SendToCustomer ?? null;
            $schedule->SendToOtherAddressesOnFailure = $data->FailureReceiptOptions->SendToOtherAddresses ?? null;
        }

        return $schedule;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->CustomerId !== null) {
            $array['CustomerId'] = $this->CustomerId;
        }
        if ($this->AccountId !== null) {
            $array['AccountId'] = $this->AccountId;
        }
        if ($this->PaymentType !== null) {
            $array['PaymentType'] = $this->PaymentType;
        }
        if ($this->PaymentSubType !== null) {
            $array['PaymentSubType'] = $this->PaymentSubType;
        }
        if ($this->PaymentAmount !== null) {
            $array['PaymentAmount'] = $this->PaymentAmount;
        }
        if ($this->FirstPaymentAmount !== null) {
            $array['FirstPaymentAmount'] = $this->FirstPaymentAmount;
        }
        if ($this->StartDate !== null) {
            $array['StartDate'] = $this->StartDate;
        }
        if ($this->EndDate !== null) {
            $array['EndDate'] = $this->EndDate;
        }
        if ($this->ExecutionFrequencyType !== null) {
            $array['ExecutionFrequencyType'] = $this->ExecutionFrequencyType;
        }
        if ($this->ExecutionFrequencyParameter !== null) {
            $array['ExecutionFrequencyParameter'] = $this->ExecutionFrequencyParameter;
        }
        if ($this->Description !== null) {
            $array['Description'] = $this->Description;
        }

        // Construct SuccessReceiptOptions for request
        $successOptions = [];
        if ($this->SendToCustomerOnSuccess !== null) {
            $successOptions['SendToCustomer'] = $this->SendToCustomerOnSuccess;
        }
        if ($this->SendToOtherAddressesOnSuccess !== null && !empty($this->SendToOtherAddressesOnSuccess)) {
            $successOptions['SendToOtherAddresses'] = $this->SendToOtherAddressesOnSuccess;
        }
        if (!empty($successOptions)) {
            $array['SuccessReceiptOptions'] = $successOptions;
        }

        // Construct FailureReceiptOptions for request
        $failureOptions = [];
        if ($this->SendToCustomerOnFailure !== null) {
            $failureOptions['SendToCustomer'] = $this->SendToCustomerOnFailure;
        }
        if ($this->SendToOtherAddressesOnFailure !== null && !empty($this->SendToOtherAddressesOnFailure)) {
            $failureOptions['SendToOtherAddresses'] = $this->SendToOtherAddressesOnFailure;
        }
        if (!empty($failureOptions)) {
            $array['FailureReceiptOptions'] = $failureOptions;
        }

        // Id is required for Update requests, not for New.
        // This method is generic for request bodies.
        // If this entity were strictly for "New", Id would be omitted.
        // If used for "Update", CustomerId and AccountId might not be updatable.
        if ($this->Id !== null) {
            $array['Id'] = $this->Id;
        }

        // Read-only fields are excluded: ScheduleStatus, NextScheduleDate, PauseUntilDate,
        // FirstPaymentDone, DateOfLastPaymentMade, TotalAmountPaid, NumberOfPaymentsMade,
        // LastModified, CreatedOn.

        return $array;
    }
}
