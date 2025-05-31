<?php

namespace PaySimple\V4\Entities;

use stdClass;
use PaySimple\V4\Entities\Address;

class Customer
{
    public ?int $Id = null;
    public ?string $FirstName = null;
    public ?string $LastName = null;
    public ?string $MiddleName = null;
    public ?string $Suffix = null;
    public ?string $Company = null;
    public ?string $CustomerAccount = null; // "Customer supplied account number/identifier"
    public ?string $Email = null;
    public ?string $AltEmail = null;
    public ?string $Phone = null;
    public ?string $AltPhone = null;
    public ?string $MobilePhone = null;
    public ?string $Fax = null;
    public ?string $Website = null;
    public ?string $Notes = null;
    public ?Address $BillingAddress = null;
    public ?Address $ShippingAddress = null;
    public bool $ShippingSameAsBilling = true; // Default based on API docs for new customer
    public ?string $LastModified = null;
    public ?string $CreatedOn = null;

    public static function fromStdClass(stdClass $data): self
    {
        $customer = new self();

        $customer->Id = $data->Id ?? null;
        $customer->FirstName = $data->FirstName ?? null;
        $customer->LastName = $data->LastName ?? null;
        $customer->MiddleName = $data->MiddleName ?? null;
        $customer->Suffix = $data->Suffix ?? null;
        $customer->Company = $data->Company ?? null;
        $customer->CustomerAccount = $data->CustomerAccount ?? null;
        $customer->Email = $data->Email ?? null;
        $customer->AltEmail = $data->AltEmail ?? null;
        $customer->Phone = $data->Phone ?? null;
        $customer->AltPhone = $data->AltPhone ?? null;
        $customer->MobilePhone = $data->MobilePhone ?? null;
        $customer->Fax = $data->Fax ?? null;
        $customer->Website = $data->Website ?? null;
        $customer->Notes = $data->Notes ?? null;

        if (isset($data->BillingAddress) && is_object($data->BillingAddress)) {
            $customer->BillingAddress = Address::fromStdClass($data->BillingAddress);
        }
        if (isset($data->ShippingAddress) && is_object($data->ShippingAddress)) {
            $customer->ShippingAddress = Address::fromStdClass($data->ShippingAddress);
        }
        // API response for GET has ShippingSameAsBilling as boolean
        if (isset($data->ShippingSameAsBilling)) {
            $customer->ShippingSameAsBilling = (bool)$data->ShippingSameAsBilling;
        }

        $customer->LastModified = $data->LastModified ?? null;
        $customer->CreatedOn = $data->CreatedOn ?? null;

        return $customer;
    }

    public function toArray(): array
    {
        $array = [];

        // Id is usually not sent in create/update, but let's include if present for completeness
        // or specific use cases. For "New Customer" it's not used.
        // For "Update Customer", the ID is in the URL, not body.
        // So, we might exclude Id generally from toArray for request bodies.
        // However, the prompt asks for "suitable for API request bodies"
        // and "Only include properties that are set (not null)".
        // For now, I'll include all set properties as per general instruction.

        // Id is typically not part of the request body for create/update customer.
        // if ($this->Id !== null) {
        //     $array['Id'] = $this->Id;
        // }
        if ($this->FirstName !== null) {
            $array['FirstName'] = $this->FirstName;
        }
        if ($this->LastName !== null) {
            $array['LastName'] = $this->LastName;
        }
        if ($this->MiddleName !== null) {
            $array['MiddleName'] = $this->MiddleName;
        }
        if ($this->Suffix !== null) {
            $array['Suffix'] = $this->Suffix;
        }
        if ($this->Company !== null) {
            $array['Company'] = $this->Company;
        }
        if ($this->CustomerAccount !== null) {
            $array['CustomerAccount'] = $this->CustomerAccount;
        }
        if ($this->Email !== null) {
            $array['Email'] = $this->Email;
        }
        if ($this->AltEmail !== null) {
            $array['AltEmail'] = $this->AltEmail;
        }
        if ($this->Phone !== null) {
            $array['Phone'] = $this->Phone;
        }
        if ($this->AltPhone !== null) {
            $array['AltPhone'] = $this->AltPhone;
        }
        if ($this->MobilePhone !== null) {
            $array['MobilePhone'] = $this->MobilePhone;
        }
        if ($this->Fax !== null) {
            $array['Fax'] = $this->Fax;
        }
        if ($this->Website !== null) {
            $array['Website'] = $this->Website;
        }
        if ($this->Notes !== null) {
            $array['Notes'] = $this->Notes;
        }

        // For new customer, "ShippingSameAsBilling" is a top-level field.
        // If true, ShippingAddress is not required.
        // If false, ShippingAddress is required. BillingAddress is always required.
        $array['ShippingSameAsBilling'] = $this->ShippingSameAsBilling;

        if ($this->BillingAddress !== null) {
            $array['BillingAddress'] = $this->BillingAddress->toArray();
        }

        // Only include ShippingAddress if ShippingSameAsBilling is false and ShippingAddress is set
        if (!$this->ShippingSameAsBilling && $this->ShippingAddress !== null) {
            $array['ShippingAddress'] = $this->ShippingAddress->toArray();
        } elseif (!$this->ShippingSameAsBilling && $this->ShippingAddress === null) {
            // This case implies ShippingAddress is required but not provided.
            // API will likely error. For toArray, we just reflect the state.
        }


        // LastModified and CreatedOn are typically not sent in request bodies
        // if ($this->LastModified !== null) {
        //     $array['LastModified'] = $this->LastModified;
        // }
        // if ($this->CreatedOn !== null) {
        //     $array['CreatedOn'] = $this->CreatedOn;
        // }

        return $array;
    }
}
