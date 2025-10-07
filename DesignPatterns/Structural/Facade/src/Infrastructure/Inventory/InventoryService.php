<?php

declare(strict_types=1);

namespace App\Infrastructure\Inventory;

use App\Domain\Order;

final class InventoryService implements InventoryServiceInterface
{
    public function reserve(Order $order): array
    {
        // SKU reservation in the warehouse/ERP
        return ['reservationId' => 'res_'.bin2hex(random_bytes(4))];
    }
}