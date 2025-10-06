<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderItemDTO
{
    public function __construct(
        #[Assert\NotBlank] public readonly string $sku,
        #[Assert\NotBlank] public readonly string $name,
        #[Assert\Positive] public readonly int $qty,
        #[Assert\PositiveOrZero] public readonly int $price // minor units
    ) {}

    /** @param array{sku:string,name:string,qty:int,price:int} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            sku: (string)($data['sku'] ?? ''),
            name: (string)($data['name'] ?? ''),
            qty: (int)($data['qty'] ?? 0),
            price: (int)($data['price'] ?? 0)
        );
    }
}
