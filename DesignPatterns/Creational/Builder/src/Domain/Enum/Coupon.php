<?php
declare(strict_types=1);

namespace App\Domain\Enum;

enum Coupon: string
{
    case WRONG_DISCOUNT = 'Wrong discount. Pass order with 0% discount';
    case WELCOME10 = 'Welcome discount with 10%';

    public function getDiscount(): float
    {
        return match ($this) {
            self::WRONG_DISCOUNT => 0,
            self::WELCOME10 => 0.10,
        };
    }

    public static function fromString(?string $value): self
    {
        if ($value === null) {
            return self::WRONG_DISCOUNT;
        }
        return match (strtoupper(trim($value))) {
            self::WELCOME10->name => self::WELCOME10,
            default => self::WRONG_DISCOUNT,
        };
    }

}
