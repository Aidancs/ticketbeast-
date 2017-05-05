<?php

namespace App\Billing;

class FakePaymentGateway
{
    public function getValidTestToken()
    {
        dd(342);
        return "valid-token";
    }
}

