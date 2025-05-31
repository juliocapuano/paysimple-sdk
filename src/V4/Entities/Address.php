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

    public static function fromStdClass(stdClass $data): self
    {
        $address = new self();

        $address->StreetAddress1 = $data->StreetAddress1 ?? null;
        $address->StreetAddress2 = $data->StreetAddress2 ?? null;
        $address->City = $data->City ?? null;

        // Handle variations in API field names for state and zip
        $address->StateCode = $data->StateCode // Prefer StateCode directly
            ?? $data->StateAbbreviation // Fallback
            ?? $data->StateProvince // Common API name
            ?? null;

        $address->ZipCode = $data->ZipCode // Prefer ZipCode directly
            ?? $data->PostalCode // Common API name
            ?? null;

        $address->Country = $data->Country ?? null;

        return $address;
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
