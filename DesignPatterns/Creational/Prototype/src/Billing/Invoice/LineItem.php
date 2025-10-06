<?php

declare(strict_types=1);

namespace App\Billing\Invoice;

use App\Billing\Value\Money;

final class LineItem
{
    public function __construct(
        public string $sku,
        public string $name,
        public int $qty,
        public Money $unitPrice
    ) {}

    public function subtotal(): Money
    {
        return new Money($this->unitPrice->amount * $this->qty, $this->unitPrice->currency);
    }

    /**
     * Deep clone of value object
     * @return void
     */
    public function __clone(): void
    {
        $this->unitPrice = new Money($this->unitPrice->amount, $this->unitPrice->currency);
    }
}
