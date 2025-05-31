<?php

namespace PaySimple\V4\Services;

use GuzzleHttp\Exception\GuzzleException;
use PaySimple\V4\Core\PaySimpleException;
use PaySimple\V4\Entities\MerchantPaymentOptions;

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
     * @return MerchantPaymentOptions
     * @throws GuzzleException|\PaySimple\V4\Core\PaySimpleException
     */
    final public function paymentOptions(): MerchantPaymentOptions
    {
        $response = $this->client->get('merchant/paymentoptions');
        if ($this->client->hasErrors() || ($response['error'] ?? false)) {
            throw PaySimpleException::fromApiResponse($response['data'] ?? [], $response['meta'] ?? (object)[]);
        }
        $apiResponseData = $response['data']; // This should be stdClass
        return MerchantPaymentOptions::fromStdClass($apiResponseData);
    }
}
