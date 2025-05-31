<?php

namespace PaySimple\V4\Entities;

use stdClass;

class MerchantPaymentOptions
{
    public ?bool $AcceptsCreditCard = null;
    public ?bool $AcceptsAch = null;
    public ?string $CreditCardIssuers = null;

    public static function fromStdClass(stdClass $data): self
    {
        $options = new self();

        $options->AcceptsCreditCard = isset($data->AcceptsCreditCard) ? (bool)$data->AcceptsCreditCard : null;
        $options->AcceptsAch = isset($data->AcceptsAch) ? (bool)$data->AcceptsAch : null;
        $options->CreditCardIssuers = $data->CreditCardIssuers ?? null;

        return $options;
    }
}
