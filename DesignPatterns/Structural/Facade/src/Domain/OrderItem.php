<?php
declare(strict_types=1);

namespace App\Domain;

final class OrderItem
{
    public function __construct(
        public readonly string $sku,
        public readonly int $qty,
        public readonly Money $pricePerUnit
    ) {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }
    }
}
