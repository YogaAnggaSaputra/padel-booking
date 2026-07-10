<?php
// app/Contracts/PaymentGateway.php

namespace App\Contracts;

interface PaymentGateway
{
    public function charge(array $params): array;
    public function webhook(array $payload): array;
}
