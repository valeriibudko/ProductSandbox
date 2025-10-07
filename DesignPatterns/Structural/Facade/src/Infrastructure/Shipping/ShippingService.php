<?php

declare(strict_types=1);

namespace App\Infrastructure\Shipping;

use App\Domain\Order;

final class ShippingService implements ShippingServiceInterface
{
    public function createShipment(Order $order): array
    {
        // integration with 3PL or courier
        return [
            'shipmentId' => 'shp_'.bin2hex(random_bytes(4)),
            'trackingNumber' => 'TRK'.random_int(1000000, 9999999)
        ];
    }
}