<?php
declare(strict_types=1);

namespace App\Application\Enum;

enum Channel: string
{
    case EMAIL = 'email';
    case SMS   = 'sms';

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'email' => self::EMAIL,
            'sms'   => self::SMS,
            default => throw new \InvalidArgumentException("Unknown channel: $value"),
        };
    }
}
