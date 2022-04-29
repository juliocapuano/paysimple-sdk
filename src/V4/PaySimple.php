<?php

namespace PaySimple\V4;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Services\MerchantService;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Services\RecurringPaymentsService;

/**
 * PaySimple SDK Instance Creator
 * @package PaySimple\V4
 */
class PaySimple
{

    /**
     * @var ApiClient
     */
    private $client;

    /**
     * @param string $user
     * @param string $token
     * @param bool $sandbox
     */
    public function __construct(string $user, string $token, bool $sandbox = false)
    {
        $config       = new Config($user, $token, $sandbox);
        $this->client = new ApiClient($config);
    }


    /**
     * Customer Api endpoints
     * @see https://documentation.paysimple.com/reference#customer-object
     * @return CustomerService
     */
    public function customers(): CustomerService
    {
        return new CustomerService($this->client);
    }

    /**
     * Accounts Api endpoints
     * @see https://documentation.paysimple.com/reference#managing-customer-payment-accounts
     * @return AccountService
     */
    public function accounts(): AccountService
    {
        return new AccountService($this->client);
    }

    /**
     * Payments Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     * @return PaymentService
     */
    public function payments(): PaymentService
    {
        return new PaymentService($this->client);
    }

    /**
     * Payments Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     * @return RecurringPaymentsService
     */
    public function recurringPayments(): RecurringPaymentsService
    {
        return new RecurringPaymentsService($this->client);
    }

    /**
     * Payments Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     * @return MerchantService
     */
    public function merchant(): MerchantService
    {
        return new MerchantService($this->client);
    }
}
