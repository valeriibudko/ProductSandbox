<?php

declare(strict_types=1);

namespace App\Domain;

final class Money
{
    public function __construct(
        public readonly int $amountMinor, // price in cents
        public readonly string $currency = 'EUR'
    ) {
        if ($amountMinor <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }
        if ($currency === '') {
            throw new \InvalidArgumentException('Currency must not be empty.');
        }
    }
}