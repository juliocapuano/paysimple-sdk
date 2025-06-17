<?php

namespace PaySimple\V4\Entities;

use stdClass;
use PaySimple\V4\Entities\ReceiptOptions;

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

    public ?ReceiptOptions $SuccessReceiptOptions = null;
    public ?ReceiptOptions $FailureReceiptOptions = null;

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

        if (isset($data->SuccessReceiptOptions) && is_object($data->SuccessReceiptOptions)) {
            $schedule->SuccessReceiptOptions = ReceiptOptions::fromStdClass($data->SuccessReceiptOptions);
        }
        if (isset($data->FailureReceiptOptions) && is_object($data->FailureReceiptOptions)) {
            $schedule->FailureReceiptOptions = ReceiptOptions::fromStdClass($data->FailureReceiptOptions);
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

        if ($this->SuccessReceiptOptions !== null) {
            $array['SuccessReceiptOptions'] = $this->SuccessReceiptOptions->toArray();
        }

        if ($this->FailureReceiptOptions !== null) {
            $array['FailureReceiptOptions'] = $this->FailureReceiptOptions->toArray();
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
