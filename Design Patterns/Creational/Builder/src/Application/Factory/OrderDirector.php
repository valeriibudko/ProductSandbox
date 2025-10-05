<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Common\Money;
use App\Domain\Order\Builder\OrderBuilderInterface;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\Value\Address;
use App\Application\DTO\OrderCreateDTO;

final readonly class OrderDirector
{
    public function __construct(private OrderBuilderInterface $builder) {}

    public function fromPayload(OrderCreateDTO $orderCreateDto): Order
    {
        $this->builder
            ->start(
                $orderCreateDto->id,
                $orderCreateDto->email,
                $orderCreateDto->currency
            )
            ->withShippingAddress(Address::of(
                $orderCreateDto->shipping->country,
                $orderCreateDto->shipping->city,
                $orderCreateDto->shipping->line1,
                $orderCreateDto->shipping->line2,
                $orderCreateDto->shipping->postal
            ))
            ->withBillingAddress(Address::of(
                $orderCreateDto->billing->country,
                $orderCreateDto->billing->city,
                $orderCreateDto->billing->line1,
                $orderCreateDto->billing->line2,
                $orderCreateDto->billing->postal
            ));

        foreach ($orderCreateDto->items as $item) {
            $this->builder->addItem(OrderItem::of(
                $item->sku,
                $item->name,
                $item->qty,
                Money::of($item->price, $orderCreateDto->currency)
            ));
        }

        if ($orderCreateDto->shippingCost !== null) {
            $this->builder->withShippingCost(Money::of($orderCreateDto->shippingCost, $orderCreateDto->currency));
        }

        if (!empty($orderCreateDto->coupon)) {
            $this->builder->withCoupon($orderCreateDto->coupon);
        }

        if (!empty($orderCreateDto->meta)) {
            $this->builder->withMeta($orderCreateDto->meta);
        }

        return $this->builder->build();
    }
}
