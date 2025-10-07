<?php
declare(strict_types=1);

namespace App\Infrastructure\PaymentGateway;

use App\Domain\Money;

interface PaymentGatewayInterface
{
    /** @return array{paymentId:string, captured:boolean} */
    public function charge(string $customerId, Money $amount): array;
}
