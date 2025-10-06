<?php

declare(strict_types=1);

namespace App\Billing\Prototype;

use App\Billing\Invoice\Invoice;

final class InvoicePrototypeRegistry
{
    /** @var array<string, Invoice> */
    private array $prototypes = [];

    public function register(string $key, Invoice $prototype): void
    {
        $this->prototypes[$key] = $prototype;
    }

    public function clone(string $key): Invoice
    {
        if (!isset($this->prototypes[$key])) {
            throw new \InvalidArgumentException("Prototype '$key' not found");
        }
        return clone $this->prototypes[$key];
    }
}
