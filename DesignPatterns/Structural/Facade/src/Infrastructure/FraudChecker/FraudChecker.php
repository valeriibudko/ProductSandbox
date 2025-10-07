<?php

declare(strict_types=1);

namespace App\Infrastructure\FraudChecker;

use App\Domain\Order;

final class FraudChecker implements FraudCheckerInterface
{
    public function check(Order $order): void
    {
        if ($order->total()->amountMinor > 5000_00) {
            throw new \RuntimeException('High-risk amount. Manual review required.');
        }
    }
}
