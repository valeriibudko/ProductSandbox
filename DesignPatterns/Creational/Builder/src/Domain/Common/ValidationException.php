<?php

declare(strict_types=1);

namespace App\Domain\Common;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    public static function withMessages(array $messages): self
    {
        return new self(implode("; ", $messages));
    }
}
