<?php

namespace PaySimple\V4\Domain\Entity;

class AchAccount
{
    private ?int $accountId = null;
    private ?int $customerId = null;
    private ?string $accountNumber = null;
    private ?string $routingNumber = null;
    private ?string $accountType = null;
    private ?string $accountHolderName = null;
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

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getRoutingNumber(): ?string
    {
        return $this->routingNumber;
    }

    public function setRoutingNumber(string $routingNumber): self
    {
        $this->routingNumber = $routingNumber;
        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getAccountHolderName(): ?string
    {
        return $this->accountHolderName;
    }

    public function setAccountHolderName(string $accountHolderName): self
    {
        $this->accountHolderName = $accountHolderName;
        return $this;
    }

    public function getBillingStreet(): ?string
    {
        return $this->billingStreet;
    }

    public function setBillingStreet(string $billingStreet): self
    {
        $this->billingStreet = $billingStreet;
        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $billingCity): self
    {
        $this->billingCity = $billingCity;
        return $this;
    }

    public function getBillingState(): ?string
    {
        return $this->billingState;
    }

    public function setBillingState(string $billingState): self
    {
        $this->billingState = $billingState;
        return $this;
    }

    public function getBillingZip(): ?string
    {
        return $this->billingZip;
    }

    public function setBillingZip(string $billingZip): self
    {
        $this->billingZip = $billingZip;
        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(string $billingCountry): self
    {
        $this->billingCountry = $billingCountry;
        return $this;
    }

    public function isDefaultAccount(): bool
    {
        return $this->isDefaultAccount;
    }

    public function setIsDefaultAccount(bool $isDefaultAccount): self
    {
        $this->isDefaultAccount = $isDefaultAccount;
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

        if ($this->accountNumber !== null) {
            $data['AccountNumber'] = $this->accountNumber;
        }

        if ($this->routingNumber !== null) {
            $data['RoutingNumber'] = $this->routingNumber;
        }

        if ($this->accountType !== null) {
            $data['AccountType'] = $this->accountType;
        }

        if ($this->accountHolderName !== null) {
            $data['AccountHolderName'] = $this->accountHolderName;
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
        $achAccount = new self();

        if (isset($data['AccountId'])) {
            $achAccount->setAccountId($data['AccountId']);
        }

        if (isset($data['CustomerId'])) {
            $achAccount->setCustomerId($data['CustomerId']);
        }

        if (isset($data['AccountNumber'])) {
            $achAccount->setAccountNumber($data['AccountNumber']);
        }

        if (isset($data['RoutingNumber'])) {
            $achAccount->setRoutingNumber($data['RoutingNumber']);
        }

        if (isset($data['AccountType'])) {
            $achAccount->setAccountType($data['AccountType']);
        }

        if (isset($data['AccountHolderName'])) {
            $achAccount->setAccountHolderName($data['AccountHolderName']);
        }

        if (isset($data['BillingStreet'])) {
            $achAccount->setBillingStreet($data['BillingStreet']);
        }

        if (isset($data['BillingCity'])) {
            $achAccount->setBillingCity($data['BillingCity']);
        }

        if (isset($data['BillingState'])) {
            $achAccount->setBillingState($data['BillingState']);
        }

        if (isset($data['BillingZip'])) {
            $achAccount->setBillingZip($data['BillingZip']);
        }

        if (isset($data['BillingCountry'])) {
            $achAccount->setBillingCountry($data['BillingCountry']);
        }

        if (isset($data['IsDefaultAccount'])) {
            $achAccount->setIsDefaultAccount($data['IsDefaultAccount']);
        }

        return $achAccount;
    }
}
