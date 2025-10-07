<?php
declare(strict_types=1);

namespace App\Infrastructure\FraudChecker;

use App\Domain\Order;

interface FraudCheckerInterface
{
    /**
     * throws an exception on suspicion
     * @param Order $order
     * @return void
     */
    public function check(Order $order): void;
}