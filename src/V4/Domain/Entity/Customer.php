<?php

namespace PaySimple\V4\Domain\Entity;

use PaySimple\V4\Domain\ValueObject\Money;

class Customer
{
    private ?int $customerId = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $company = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $fax = null;
    private ?string $street = null;
    private ?string $city = null;
    private ?string $state = null;
    private ?string $zip = null;
    private ?string $country = null;
    private ?string $taxCode = null;
    private bool $sendStatement = false;

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function setTaxCode(string $taxCode): self
    {
        $this->taxCode = $taxCode;
        return $this;
    }

    public function isSendStatement(): bool
    {
        return $this->sendStatement;
    }

    public function setSendStatement(bool $sendStatement): self
    {
        $this->sendStatement = $sendStatement;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->customerId !== null) {
            $data['CustomerId'] = $this->customerId;
        }

        if ($this->firstName !== null) {
            $data['FirstName'] = $this->firstName;
        }

        if ($this->lastName !== null) {
            $data['LastName'] = $this->lastName;
        }

        if ($this->company !== null) {
            $data['Company'] = $this->company;
        }

        if ($this->email !== null) {
            $data['Email'] = $this->email;
        }

        if ($this->phone !== null) {
            $data['Phone'] = $this->phone;
        }

        if ($this->fax !== null) {
            $data['Fax'] = $this->fax;
        }

        if ($this->street !== null) {
            $data['Street'] = $this->street;
        }

        if ($this->city !== null) {
            $data['City'] = $this->city;
        }

        if ($this->state !== null) {
            $data['State'] = $this->state;
        }

        if ($this->zip !== null) {
            $data['Zip'] = $this->zip;
        }

        if ($this->country !== null) {
            $data['Country'] = $this->country;
        }

        if ($this->taxCode !== null) {
            $data['TaxCode'] = $this->taxCode;
        }

        if ($this->sendStatement) {
            $data['SendStatement'] = $this->sendStatement;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        $customer = new self();

        if (isset($data['CustomerId'])) {
            $customer->setCustomerId($data['CustomerId']);
        }

        if (isset($data['FirstName'])) {
            $customer->setFirstName($data['FirstName']);
        }

        if (isset($data['LastName'])) {
            $customer->setLastName($data['LastName']);
        }

        if (isset($data['Company'])) {
            $customer->setCompany($data['Company']);
        }

        if (isset($data['Email'])) {
            $customer->setEmail($data['Email']);
        }

        if (isset($data['Phone'])) {
            $customer->setPhone($data['Phone']);
        }

        if (isset($data['Fax'])) {
            $customer->setFax($data['Fax']);
        }

        if (isset($data['Street'])) {
            $customer->setStreet($data['Street']);
        }

        if (isset($data['City'])) {
            $customer->setCity($data['City']);
        }

        if (isset($data['State'])) {
            $customer->setState($data['State']);
        }

        if (isset($data['Zip'])) {
            $customer->setZip($data['Zip']);
        }

        if (isset($data['Country'])) {
            $customer->setCountry($data['Country']);
        }

        if (isset($data['TaxCode'])) {
            $customer->setTaxCode($data['TaxCode']);
        }

        if (isset($data['SendStatement'])) {
            $customer->setSendStatement($data['SendStatement']);
        }

        return $customer;
    }
}
