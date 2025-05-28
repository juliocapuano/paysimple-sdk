<?php

namespace PaySimple\V4\Entities;

use stdClass;

class CreditCard
{
    // Fields from GET response and general properties
    public ?int $Id = null;
    public ?int $CustomerId = null;
    public ?string $Issuer = null; // e.g., "VISA"
    public ?string $LastFour = null;
    public ?bool $IsDefault = null;
    public ?string $LastModified = null;
    public ?string $CreatedOn = null;
    public ?string $CardType = null; // e.g., "Visa", "Mastercard". Redundant with Issuer sometimes but present in docs.

    // Fields for POST/PUT requests (and also present in GET responses, often masked or partial)
    public ?string $CreditCardNumber = null; // Full number for request if not using Token; Masked in response
    public ?string $ExpirationDate = null;   // MMYY or MM/YYYY. API expects MMYY for requests.
    public ?object $BillingAddress = null;   // stdClass or specific Address entity
    public ?string $BillingZipCode = null;   // Optional: Can be part of BillingAddress object or standalone for some API calls.
                                            // The docs for "New Credit Card" show it as a top-level field for request.

    // Fields primarily for POST requests
    public ?string $Token = null;
    public ?string $Cvc = null; // Not stored, only for request

    public static function fromStdClass(stdClass $data): self
    {
        $creditCard = new self();

        $creditCard->Id = $data->Id ?? null;
        $creditCard->CustomerId = $data->CustomerId ?? null;
        $creditCard->Issuer = $data->Issuer ?? null;
        $creditCard->LastFour = $data->LastFour ?? null;
        $creditCard->IsDefault = $data->IsDefault ?? null;
        $creditCard->LastModified = $data->LastModified ?? null;
        $creditCard->CreatedOn = $data->CreatedOn ?? null;
        $creditCard->CardType = $data->CardType ?? $data->Issuer ?? null; // Fallback CardType to Issuer if not present

        // CreditCardNumber in response is usually masked, e.g., "XXXXXXXXXXXX1234"
        // For fromStdClass, we'll take whatever is provided.
        $creditCard->CreditCardNumber = $data->CreditCardNumber ?? null;
        $creditCard->ExpirationDate = $data->ExpirationDate ?? null; // Response format might be MM/YYYY

        if (isset($data->BillingAddress) && is_object($data->BillingAddress)) {
            $creditCard->BillingAddress = $data->BillingAddress;
        }
        $creditCard->BillingZipCode = $data->BillingZipCode ?? null;

        // Token and Cvc are not expected in responses
        // $creditCard->Token = $data->Token ?? null; // Not in GET response
        // $creditCard->Cvc = $data->Cvc ?? null; // Not in GET response

        return $creditCard;
    }

    public function toArray(): array
    {
        $array = [];

        // For creating/updating, CustomerId is essential
        if ($this->CustomerId !== null) {
            $array['CustomerId'] = $this->CustomerId;
        }

        // Either Token or CreditCardNumber/ExpirationDate should be provided for new cards
        if ($this->Token !== null) {
            $array['Token'] = $this->Token;
        } else {
            if ($this->CreditCardNumber !== null) {
                $array['CreditCardNumber'] = $this->CreditCardNumber;
            }
            if ($this->ExpirationDate !== null) {
                // API for new card expects MMYY
                $array['ExpirationDate'] = str_replace('/', '', $this->ExpirationDate);
            }
        }

        if ($this->Cvc !== null) {
            $array['Cvc'] = $this->Cvc;
        }

        // BillingAddress is required for new card if not using Token for some gateways
        // Or if updating address details.
        if ($this->BillingAddress !== null) {
            $array['BillingAddress'] = (array)$this->BillingAddress;
        }

        // BillingZipCode is specifically mentioned for "New Credit Card" request
        if ($this->BillingZipCode !== null) {
            $array['BillingZipCode'] = $this->BillingZipCode;
        }
        
        // IsDefault can be set during creation or update
        if ($this->IsDefault !== null) {
            $array['IsDefault'] = $this->IsDefault;
        }

        // Id is typically in URL for updates, but some APIs might take it in body.
        // For "New Credit Card" it's not used. For "Update Credit Card", ID is in body.
        if ($this->Id !== null) {
             $array['Id'] = $this->Id; // Required for Update Credit Card
        }


        // Read-only fields like LastFour, CardType, Issuer, LastModified, CreatedOn are excluded.

        return $array;
    }
}
