<?php

namespace PaySimple\V4\Domain\Entity;

use PaySimple\V4\Domain\ValueObject\CreditCardNumber;
use PaySimple\V4\Domain\ValueObject\ExpirationDate;

class CreditCard
{
    private ?int $accountId = null;
    private ?int $customerId = null;
    private ?CreditCardNumber $creditCardNumber = null;
    private ?ExpirationDate $expirationDate = null;
    private ?string $cvv = null;
    private ?string $cardholderName = null;
    private ?string $billingStreet = null;
    private ?string $billingCity = null;
    private ?string $billingState = null;
    private ?string $billingZip = null;
    private ?string $billingCountry = null;
    private bool $isDefaultAccount = false;

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;
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

    public function getCreditCardNumber(): ?CreditCardNumber
    {
        return $this->creditCardNumber;
    }

    public function setCreditCardNumber(string|CreditCardNumber $number): self
    {
        if (is_string($number)) {
            $this->creditCardNumber = CreditCardNumber::fromString($number);
        } else {
            $this->creditCardNumber = $number;
        }
        return $this;
    }

    public function getExpirationDate(): ?ExpirationDate
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(string|ExpirationDate $date): self
    {
        if (is_string($date)) {
            $this->expirationDate = ExpirationDate::fromString($date);
        } else {
            $this->expirationDate = $date;
        }
        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }

    public function getCardholderName(): ?string
    {
        return $this->cardholderName;
    }

    public function setCardholderName(string $name): self
    {
        $this->cardholderName = $name;
        return $this;
    }

    public function getBillingStreet(): ?string
    {
        return $this->billingStreet;
    }

    public function setBillingStreet(string $street): self
    {
        $this->billingStreet = $street;
        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $city): self
    {
        $this->billingCity = $city;
        return $this;
    }

    public function getBillingState(): ?string
    {
        return $this->billingState;
    }

    public function setBillingState(string $state): self
    {
        $this->billingState = $state;
        return $this;
    }

    public function getBillingZip(): ?string
    {
        return $this->billingZip;
    }

    public function setBillingZip(string $zip): self
    {
        $this->billingZip = $zip;
        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(string $country): self
    {
        $this->billingCountry = $country;
        return $this;
    }

    public function isDefaultAccount(): bool
    {
        return $this->isDefaultAccount;
    }

    public function setIsDefaultAccount(bool $isDefault): self
    {
        $this->isDefaultAccount = $isDefault;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->accountId !== null) {
            $data['AccountId'] = $this->accountId;
        }

        if ($this->customerId !== null) {
            $data['CustomerId'] = $this->customerId;
        }

        if ($this->creditCardNumber !== null) {
            $data['CreditCardNumber'] = $this->creditCardNumber->toString();
        }

        if ($this->expirationDate !== null) {
            $data['ExpirationDate'] = $this->expirationDate->toString();
        }

        if ($this->cvv !== null) {
            $data['Cvv'] = $this->cvv;
        }

        if ($this->cardholderName !== null) {
            $data['CardholderName'] = $this->cardholderName;
        }

        if ($this->billingStreet !== null) {
            $data['BillingStreet'] = $this->billingStreet;
        }

        if ($this->billingCity !== null) {
            $data['BillingCity'] = $this->billingCity;
        }

        if ($this->billingState !== null) {
            $data['BillingState'] = $this->billingState;
        }

        if ($this->billingZip !== null) {
            $data['BillingZip'] = $this->billingZip;
        }

        if ($this->billingCountry !== null) {
            $data['BillingCountry'] = $this->billingCountry;
        }

        if ($this->isDefaultAccount) {
            $data['IsDefaultAccount'] = $this->isDefaultAccount;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        $creditCard = new self();

        if (isset($data['AccountId'])) {
            $creditCard->setAccountId($data['AccountId']);
        }

        if (isset($data['CustomerId'])) {
            $creditCard->setCustomerId($data['CustomerId']);
        }

        if (isset($data['CreditCardNumber'])) {
            $creditCard->setCreditCardNumber($data['CreditCardNumber']);
        }

        if (isset($data['ExpirationDate'])) {
            $creditCard->setExpirationDate($data['ExpirationDate']);
        }

        if (isset($data['Cvv'])) {
            $creditCard->setCvv($data['Cvv']);
        }

        if (isset($data['CardholderName'])) {
            $creditCard->setCardholderName($data['CardholderName']);
        }

        if (isset($data['BillingStreet'])) {
            $creditCard->setBillingStreet($data['BillingStreet']);
        }

        if (isset($data['BillingCity'])) {
            $creditCard->setBillingCity($data['BillingCity']);
        }

        if (isset($data['BillingState'])) {
            $creditCard->setBillingState($data['BillingState']);
        }

        if (isset($data['BillingZip'])) {
            $creditCard->setBillingZip($data['BillingZip']);
        }

        if (isset($data['BillingCountry'])) {
            $creditCard->setBillingCountry($data['BillingCountry']);
        }

        if (isset($data['IsDefaultAccount'])) {
            $creditCard->setIsDefaultAccount($data['IsDefaultAccount']);
        }

        return $creditCard;
    }
}

