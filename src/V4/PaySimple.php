<?php

namespace PaySimple\V4;

use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Services\PaymentService;

class PaySimple
{

    /**
     * @var ApiClient
     */

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->client = new ApiClient($config);
    }


    /**
     * @see https://documentation.paysimple.com/reference#customer-object
     * @return CustomerService
     */
    public function customers()
    {
        return new CustomerService($this->client);
    }

    /**
     * @see https://documentation.paysimple.com/reference#managing-customer-payment-accounts
     * @return AccountService
     */
    public function accounts()
    {
        return new AccountService($this->client);
    }

    /**
     * @see https://documentation.paysimple.com/reference#payment
     * @return PaymentService
     */
    public function payments()
    {
        return new PaymentService($this->client);
    }

    public function recurringPayments()
    {
    }

    public function merchant()
    {
    }
}
