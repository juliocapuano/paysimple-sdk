<?php

namespace PaySimple\Tests\V4;

use PaySimple\V4\PaySimple;
use PaySimple\V4\Services\AccountService;
use PaySimple\V4\Services\CustomerService;
use PaySimple\V4\Services\MerchantService;
use PaySimple\V4\Services\PaymentService;
use PaySimple\V4\Services\RecurringPaymentsService;
use PHPUnit\Framework\TestCase;

class PaySimpleTest extends TestCase
{
    public function testServiceAccessors(): void
    {
        $paysimple = new PaySimple('user_id', 'api_key', true);

        $this->assertInstanceOf(CustomerService::class, $paysimple->customers());
        $this->assertInstanceOf(AccountService::class, $paysimple->accounts());
        $this->assertInstanceOf(PaymentService::class, $paysimple->payments());
        $this->assertInstanceOf(RecurringPaymentsService::class, $paysimple->recurringPayments());
        $this->assertInstanceOf(MerchantService::class, $paysimple->merchant());
    }
}
