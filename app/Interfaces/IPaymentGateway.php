<?php

namespace App\Interfaces;

interface IPaymentGateway
{
    public function c2b($attributes);
    // public function b2c($attributes);
}
