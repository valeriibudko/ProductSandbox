<?php

declare(strict_types=1);

namespace App\Domain\Common;

final class Money
{
    public readonly string $currency;
    public readonly int $amount; // minor units (cents)

    private function __construct(int $amount, string $currency)
    {
        if ($amount < 0) {
            throw ValidationException::withMessages(['Money amount must be >= 0 Input is: '. $amount]);
        }
        // TODO Make validation with Domain/Enum/Currency
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw ValidationException::withMessages(['Currency must be ISO 4217 (e.g., USD, EUR)']);
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function of(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw ValidationException::withMessages(['Currency mismatch']);
        }
    }
}
