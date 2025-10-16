<?php
declare(strict_types=1);

namespace App\Application\Enum;

enum Region: string
{
    case EU = 'email';
    case US   = 'sms';

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'email' => self::EMAIL,
            'sms'   => self::SMS,
            default => throw new \InvalidArgumentException("Unknown channel: $value"),
        };
    }
}
