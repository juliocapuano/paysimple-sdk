<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Class MerchantService
 * @package PaySimple\V4\Services
 * @see
 */
class MerchantService extends Service
{

    /**
     * Get enabled credit card and payment types for merchant
     *
     * @see https://documentation.paysimple.com/reference/payment-options
     * @return array
     * @throws GuzzleException
     */
    final public function paymentOptions(): array
    {
        return $this->client->get('merchant/paymentoptions');
    }
}
