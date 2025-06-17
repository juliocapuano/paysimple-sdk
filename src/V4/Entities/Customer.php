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

    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            $this->Id = isset($data['Id']) ? (int)$data['Id'] : null;
            $this->FirstName = $data['FirstName'] ?? null;
            $this->LastName = $data['LastName'] ?? null;
            $this->MiddleName = $data['MiddleName'] ?? null;
            $this->Suffix = $data['Suffix'] ?? null;
            $this->Company = $data['Company'] ?? null;
            $this->CustomerAccount = $data['CustomerAccount'] ?? null;
            $this->Email = $data['Email'] ?? null;
            $this->AltEmail = $data['AltEmail'] ?? null;
            $this->Phone = $data['Phone'] ?? null;
            $this->AltPhone = $data['AltPhone'] ?? null;
            $this->MobilePhone = $data['MobilePhone'] ?? null;
            $this->Fax = $data['Fax'] ?? null;
            $this->Website = $data['Website'] ?? null;
            $this->Notes = $data['Notes'] ?? null;

            if (isset($data['BillingAddress'])) {
                if (is_array($data['BillingAddress'])) {
                    $this->BillingAddress = new Address($data['BillingAddress']);
                } elseif (is_object($data['BillingAddress'])) { // Handles stdClass from (array)$apiResponseData
                    $this->BillingAddress = new Address((array)$data['BillingAddress']);
                }
            }
            if (isset($data['ShippingAddress'])) {
                if (is_array($data['ShippingAddress'])) {
                    $this->ShippingAddress = new Address($data['ShippingAddress']);
                } elseif (is_object($data['ShippingAddress'])) { // Handles stdClass
                    $this->ShippingAddress = new Address((array)$data['ShippingAddress']);
                }
            }

            // Default to true if not present in $data, matching existing property default
            $this->ShippingSameAsBilling = isset($data['ShippingSameAsBilling']) ? (bool)$data['ShippingSameAsBilling'] : true;

            $this->LastModified = $data['LastModified'] ?? null;
            $this->CreatedOn = $data['CreatedOn'] ?? null;
        }
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
