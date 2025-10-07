<?php

declare(strict_types=1);

namespace App\Infrastructure\Invoice;

use App\Domain\Order;

final class InvoiceService implements InvoiceServiceInterface
{
    public function issueInvoice(Order $order, string $paymentId): array
    {
        return [
            'invoiceId' => 'inv_'.bin2hex(random_bytes(4)),
            'url'       => 'https://invoices.local/'.$paymentId
        ];
    }
}