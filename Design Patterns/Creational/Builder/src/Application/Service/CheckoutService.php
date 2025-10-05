<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\OrderCreateDTO;
use App\Application\Factory\OrderDirector;
use App\Domain\Order\Order;

final class CheckoutService
{
    public function __construct(private readonly OrderDirector $orderDirector) {}

    public function createOrder(OrderCreateDTO $orderCreateDto): Order
    {
        // TODO Limit, anti fraud, ACL
        $order = $this->orderDirector->fromPayload($orderCreateDto);

        // TODO Saving to the database/calls to various services are omitted
        // $this->orders->save($order);
        // $this->outbox->publish(OrderCreated::from($order));

        return $order;
    }
}
