<?php

declare(strict_types=1);

namespace App\Billing\Value;

final class Address
{
    public function __construct(
        public readonly string $company,
        public readonly string $line1,
        public readonly ?string $line2,
        public readonly string $city,
        public readonly string $country,
        public readonly string $postal
    ) {}
}
