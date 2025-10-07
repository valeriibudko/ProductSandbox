<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class CheckoutResultDTO
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $paymentId,
        public readonly string $invoiceId,
        public readonly string $invoiceUrl,
        public readonly string $shipmentId,
        public readonly string $trackingNumber,
        public readonly string $reservationId
    ) {}
}
