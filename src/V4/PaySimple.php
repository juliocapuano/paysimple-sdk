<?php

declare(strict_types=1);

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
    private ApiClient $client;

    public function __construct(string $user, string $token, bool $sandbox = false)
    {
        $config       = new Config($user, $token, $sandbox);
        $this->client = new ApiClient($config);
    }

    /**
     * Customer Api endpoints
     * @see https://documentation.paysimple.com/reference#customer-object
     */
    public function customers(): CustomerService
    {
        return new CustomerService($this->client);
    }

    /**
     * Accounts Api endpoints
     * @see https://documentation.paysimple.com/reference#managing-customer-payment-accounts
     */
    public function accounts(): AccountService
    {
        return new AccountService($this->client);
    }

    /**
     * Payments Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     */
    public function payments(): PaymentService
    {
        return new PaymentService($this->client);
    }

    /**
     * Recurring Payments Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     */
    public function recurringPayments(): RecurringPaymentsService
    {
        return new RecurringPaymentsService($this->client);
    }

    /**
     * Merchant Api endpoints
     * @see https://documentation.paysimple.com/reference#payment
     */
    public function merchant(): MerchantService
    {
        return new MerchantService($this->client);
    }
}
