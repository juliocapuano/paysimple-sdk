<?php

namespace PaySimple\V4\Entities;

use stdClass;

class ReceiptOptions
{
    public ?bool $SendToCustomer = null;
    public ?array $SendToOtherAddresses = null; // array of strings

    public static function fromStdClass(stdClass $data): self
    {
        $options = new self();

        $options->SendToCustomer = isset($data->SendToCustomer) ? (bool)$data->SendToCustomer : null;

        if (isset($data->SendToOtherAddresses)) {
            // Ensure it's an array. If JSON provides an empty object for an empty list,
            // it might be decoded as stdClass.
            if (is_object($data->SendToOtherAddresses)) {
                $options->SendToOtherAddresses = (array)$data->SendToOtherAddresses;
            } elseif (is_array($data->SendToOtherAddresses)) {
                $options->SendToOtherAddresses = $data->SendToOtherAddresses;
            } else {
                // Handle cases where it might be a single string or other unexpected type if necessary,
                // or leave as null if type is wrong. For now, assume array or object.
                $options->SendToOtherAddresses = null;
            }
        } else {
            $options->SendToOtherAddresses = null;
        }

        // If SendToOtherAddresses became an empty array from (array)stdClass and we prefer null for empty,
        // we could add: if (empty($options->SendToOtherAddresses)) $options->SendToOtherAddresses = null;
        // However, an empty array is often a valid representation of an empty list.

        return $options;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->SendToCustomer !== null) {
            $array['SendToCustomer'] = $this->SendToCustomer;
        }

        // Only include SendToOtherAddresses if it's not null.
        // An empty array [] is a valid value to send if it was explicitly set to empty.
        // If it's null, it means "don't send this parameter".
        if ($this->SendToOtherAddresses !== null) {
            $array['SendToOtherAddresses'] = $this->SendToOtherAddresses;
        }

        return $array;
    }
}
