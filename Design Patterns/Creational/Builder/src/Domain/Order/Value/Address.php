<?php
declare(strict_types=1);

namespace App\Domain\Order\Value;

use App\Domain\Common\ValidationException;

final readonly class Address
{
    public string $country;
    public string $city;
    public string $line1;
    public ?string $line2;
    public string $postalCode;

    private function __construct(
        string $country,
        string $city,
        string $line1,
        ?string $line2,
        string $postalCode
    ) {
        $errors = [];
        if ($country === '') $errors[] = 'Country is required';
        if ($city === '') $errors[] = 'City is required';
        if ($line1 === '') $errors[] = 'Address line1 is required';
        if ($postalCode === '') $errors[] = 'Postal code is required';
        if ($errors) throw ValidationException::withMessages($errors);

        $this->country = strtoupper($country);
        $this->city = $city;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->postalCode = $postalCode;
    }

    public static function of(
        string $country,
        string $city,
        string $line1,
        ?string $line2,
        string $postalCode
    ): self {
        return new self($country, $city, $line1, $line2, $postalCode);
    }
}
