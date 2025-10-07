<?php

declare(strict_types=1);

namespace App\Infrastructure\Inventory;

use App\Domain\Order;

interface InventoryServiceInterface
{
    /** @return array{reservationId:string} */
    public function reserve(Order $order): array;
}