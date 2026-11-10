<?php

namespace PaySimple\V4;

use PaySimple\V4\Core\ApiClient;
use PaySimple\V4\Core\Config;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Services\MerchantService;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Services\RecurringPaymentsService;

class PaySimple
{
    private ApiClient $client;

    public function __construct(string $user, string $token, bool $sandbox = false)
    {
        $config       = new Config($user, $token, $sandbox);
        $this->client = new ApiClient($config);
    }

    public function customers(): CustomerService
    {
        return new CustomerService($this->client);
    }

    public function accounts(): AccountService
    {
        return new AccountService($this->client);
    }

    public function payments(): PaymentService
    {
        return new PaymentService($this->client);
    }

    public function recurringPayments(): RecurringPaymentsService
    {
        return new RecurringPaymentsService($this->client);
    }

    public function merchant(): MerchantService
    {
        return new MerchantService($this->client);
    }
}
