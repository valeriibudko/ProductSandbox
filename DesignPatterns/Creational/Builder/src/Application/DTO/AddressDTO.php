<?php
declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class AddressDTO
{
    public function __construct(
        #[Assert\NotBlank] public readonly string $country,
        #[Assert\NotBlank] public readonly string $city,
        #[Assert\NotBlank] public readonly string $line1,
        public readonly ?string $line2,
        #[Assert\NotBlank] public readonly string $postal
    ) {}

    /** @param array{country:string,city:string,line1:string,line2?:string,postal:string} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            country: (string)($data['country'] ?? ''),
            city: (string)($data['city'] ?? ''),
            line1: (string)($data['line1'] ?? ''),
            line2: array_key_exists('line2', $data) ? (string)$data['line2'] : null,
            postal: (string)($data['postal'] ?? '')
        );
    }
}
