<?php
declare(strict_types=1);

namespace App\Domain;

final class Order
{
    /** @param OrderItem[] $items */
    public function __construct(
        public readonly string $id,
        public readonly Customer $customer,
        public readonly Address $shippingAddress,
        public readonly array $items
    ) {
        if ($this->items === []) {
            throw new \InvalidArgumentException('Order must have at least one item.');
        }
    }

    public function total(): Money
    {
        $sum = 0;
        $currency = $this->items[0]->pricePerUnit->currency;
        foreach ($this->items as $item) {
            if ($item->pricePerUnit->currency !== $currency) {
                throw new \RuntimeException('Mixed currencies are not supported.');
            }
            $sum += $item->pricePerUnit->amountMinor * $item->qty;
        }
        return new Money($sum, $currency);
    }
}
