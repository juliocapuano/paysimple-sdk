<?php

namespace PaySimple\V4\Entities;

use stdClass;

class Address
{
    public ?string $StreetAddress1 = null;
    public ?string $StreetAddress2 = null;
    public ?string $City = null;
    public ?string $StateCode = null;    // Internal representation
    public ?string $ZipCode = null;      // Internal representation
    public ?string $Country = null;

    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            $this->StreetAddress1 = $data['StreetAddress1'] ?? $data['streetaddress1'] ?? null;
            $this->StreetAddress2 = $data['StreetAddress2'] ?? $data['streetaddress2'] ?? null;
            $this->City = $data['City'] ?? $data['city'] ?? null;

            // Handle mapping from API keys like 'StateProvince' to 'StateCode'
            // Also check for direct internal names if data comes from an internal source (e.g. toArray then new Address)
            $this->StateCode = $data['StateCode'] ?? $data['statecode']
                ?? $data['StateProvince'] ?? $data['stateprovince']
                ?? null;

            // Handle mapping from API keys like 'PostalCode' to 'ZipCode'
            $this->ZipCode = $data['ZipCode'] ?? $data['zipcode']
                ?? $data['PostalCode'] ?? $data['postalcode']
                ?? null;

            $this->Country = $data['Country'] ?? $data['country'] ?? null;
        }
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->StreetAddress1 !== null) {
            $array['StreetAddress1'] = $this->StreetAddress1;
        }
        if ($this->StreetAddress2 !== null) {
            $array['StreetAddress2'] = $this->StreetAddress2;
        }
        if ($this->City !== null) {
            $array['City'] = $this->City;
        }

        // Use API preferred names for request bodies
        if ($this->StateCode !== null) {
            $array['StateProvince'] = $this->StateCode;
        }
        if ($this->ZipCode !== null) {
            $array['PostalCode'] = $this->ZipCode;
        }
        if ($this->Country !== null) {
            $array['Country'] = $this->Country;
        }

        return $array;
    }
}
