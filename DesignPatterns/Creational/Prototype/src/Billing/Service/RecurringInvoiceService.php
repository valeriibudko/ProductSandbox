<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Invoice\Invoice;
use App\Billing\Prototype\InvoicePrototypeRegistry;
use DateInterval;
use DateTimeImmutable;

final class RecurringInvoiceService
{
    public function __construct(
        private readonly InvoicePrototypeRegistry $registry
    ) {}

    /**
     * Create a new bill based on template. Change a number, dates and items
     *
     * @param array<string,int> $qtyOverrides ['SKU-1' => 3, ...]
     */
    public function createFromPrototype(
        string $prototypeKey,
        string $newNumber,
        DateTimeImmutable $issuedAt,
        ?array $qtyOverrides = null
    ): Invoice {
        $invoice = $this->registry->clone($prototypeKey)
            ->withNumber($newNumber)
            ->withIssueDates($issuedAt, $issuedAt->add(new DateInterval('P14D')));

        if ($qtyOverrides) {
            // Modification is free: object is deep cloned
            $arr = $invoice->toArray()['items'];
            // TODO Create getItems()
        }

        return $invoice;
    }
}
