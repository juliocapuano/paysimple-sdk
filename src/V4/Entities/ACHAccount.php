<?php

namespace PaySimple\V4\Entities;

use stdClass;
use PaySimple\V4\Entities\Address;

class ACHAccount
{
    // Fields from GET response and general properties
    public ?int $Id = null;
    public ?int $CustomerId = null;
    public ?string $BankName = null;
    public ?string $AccountType = null; // Response: "Checking" or "Savings"
    public ?string $LastFour = null;    // Response: Last four of AccountNumber
    public ?bool $IsDefault = null;
    public ?string $LastModified = null;
    public ?string $CreatedOn = null;

    // Fields for POST/PUT requests (and also present in GET responses, often masked or partial)
    public ?string $RoutingNumber = null;
    public ?string $AccountNumber = null;   // Full number for request; Masked in response
    public ?bool $IsCheckingAccount = null; // Request: true for Checking, false for Savings
    public ?Address $BillingAddress = null;

    public static function fromStdClass(stdClass $data): self
    {
        $achAccount = new self();

        $achAccount->Id = $data->Id ?? null;
        $achAccount->CustomerId = $data->CustomerId ?? null;
        $achAccount->BankName = $data->BankName ?? null;
        $achAccount->LastFour = $data->LastFour ?? null;
        $achAccount->IsDefault = $data->IsDefault ?? null;
        $achAccount->LastModified = $data->LastModified ?? null;
        $achAccount->CreatedOn = $data->CreatedOn ?? null;

        // AccountNumber in response is usually masked, e.g., "XXXXXXXXXXXX1234"
        $achAccount->AccountNumber = $data->AccountNumber ?? null;
        $achAccount->RoutingNumber = $data->RoutingNumber ?? null; // Usually not in GET, but good to have

        // Map IsCheckingAccount from response to AccountType for entity representation
        if (isset($data->IsCheckingAccount)) {
            $achAccount->IsCheckingAccount = (bool)$data->IsCheckingAccount;
            $achAccount->AccountType = $achAccount->IsCheckingAccount ? 'Checking' : 'Savings';
        } elseif (isset($data->AccountType)) { // If API directly provides AccountType
            $achAccount->AccountType = $data->AccountType;
            $achAccount->IsCheckingAccount = ($data->AccountType === 'Checking');
        }


        if (isset($data->BillingAddress) && is_object($data->BillingAddress)) {
            $achAccount->BillingAddress = new Address((array)$data->BillingAddress);
        }

        return $achAccount;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->CustomerId !== null) {
            $array['CustomerId'] = $this->CustomerId;
        }
        if ($this->RoutingNumber !== null) {
            $array['RoutingNumber'] = $this->RoutingNumber;
        }
        if ($this->AccountNumber !== null) {
            // Send full account number for requests, not the masked version
            $array['AccountNumber'] = $this->AccountNumber;
        }
        if ($this->BankName !== null) {
            $array['BankName'] = $this->BankName;
        }

        // For requests, use IsCheckingAccount. If AccountType was set, derive IsCheckingAccount.
        if ($this->IsCheckingAccount !== null) {
            $array['IsCheckingAccount'] = $this->IsCheckingAccount;
        } elseif ($this->AccountType !== null) { // Developer might have set AccountType
            $array['IsCheckingAccount'] = ($this->AccountType === 'Checking');
        }

        if ($this->BillingAddress !== null) {
            $array['BillingAddress'] = $this->BillingAddress->toArray();
        }

        if ($this->IsDefault !== null) {
            $array['IsDefault'] = $this->IsDefault;
        }

        // Id is required for "Update ACH Account" in the body
        if ($this->Id !== null) {
             $array['Id'] = $this->Id;
        }

        // Read-only fields like LastFour, LastModified, CreatedOn are excluded.
        // AccountType is also a response field, request uses IsCheckingAccount.

        return $array;
    }
}
