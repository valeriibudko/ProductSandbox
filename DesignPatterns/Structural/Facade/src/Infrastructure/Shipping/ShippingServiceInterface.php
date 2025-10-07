<?php
declare(strict_types=1);

namespace App\Infrastructure\Shipping;

use App\Domain\Order;

interface ShippingServiceInterface
{
    /** @return array{shipmentId:string, trackingNumber:string} */
    public function createShipment(Order $order): array;
}