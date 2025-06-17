<?php

namespace PaySimple\V4\Entities;

use stdClass;

class PaymentFailureData
{
    public ?string $Code = null;
    public ?string $Description = null;
    public ?string $MerchantActionText = null;
    public ?bool $IsDecline = null;

    public static function fromStdClass(stdClass $data): self
    {
        $failureData = new self();

        $failureData->Code = $data->Code ?? null;
        $failureData->Description = $data->Description ?? null;
        $failureData->MerchantActionText = $data->MerchantActionText ?? null;
        $failureData->IsDecline = isset($data->IsDecline) ? (bool)$data->IsDecline : null;

        return $failureData;
    }
}
