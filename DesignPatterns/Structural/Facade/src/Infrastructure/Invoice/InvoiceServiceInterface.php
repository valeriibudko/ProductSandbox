<?php
declare(strict_types=1);

namespace App\Infrastructure\Invoice;

use App\Domain\Order;

interface InvoiceServiceInterface
{
    /** @return array{invoiceId:string, url:string} */
    public function issueInvoice(Order $order, string $paymentId): array;
}