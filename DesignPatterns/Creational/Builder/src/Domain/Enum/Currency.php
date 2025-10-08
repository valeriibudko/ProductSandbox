<?php
declare(strict_types=1);

namespace App\Domain\Enum;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'EUR' => self::EUR,
            'USD'   => self::USD,
            default => throw new \InvalidArgumentException("Unknown currency: $value"),
        };
    }
}
