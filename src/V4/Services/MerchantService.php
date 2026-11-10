<?php

namespace PaySimple\V4\Services;

class MerchantService extends Service
{
    final public function paymentOptions(): array
    {
        return $this->client->get('merchant/paymentoptions');
    }
}
