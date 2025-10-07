<?php

declare(strict_types=1);

namespace App\Infrastructure\PaymentGateway;

use App\Domain\Money;

final class PaymentGateway implements PaymentGatewayInterface
{
    public function charge(string $customerId, Money $amount): array
    {
        // TODO integration with Stripe/Adyen/etc
        return ['paymentId' => 'pay_'.bin2hex(random_bytes(4)), 'captured' => true];
    }
}