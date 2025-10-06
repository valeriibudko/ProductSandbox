<?php

declare(strict_types=1);

namespace App\Billing\Value;

use App\Billing\Exception\DomainException;

final class Money
{
    public function __construct(
        public readonly int $amount, // minor units
        public readonly string $currency // ISO 4217
    ) {
        if ($amount < 0) throw new DomainException('Money amount must be >= 0');
        if (!preg_match('/^[A-Z]{3}$/', $currency)) throw new DomainException('Invalid currency');
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public static function zero(string $currency): self
    {
        return new self(0, $currency);
    }
}
