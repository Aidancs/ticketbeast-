<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        $this->charges = $amount;
    }

    public function totalCharges()
    {
        return is_int($this->charges) ? $this->charges : $this->charges->sum();
    }
}

