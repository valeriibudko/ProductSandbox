<?php
declare(strict_types=1);

namespace App\Domain;

final class Address
{
    public function __construct(
        public readonly string $line1,
        public readonly ?string $line2,
        public readonly string $city,
        public readonly string $postalCode,
        public readonly string $countryCode // ISO 3166-1 alpha-2
    ) {}
}