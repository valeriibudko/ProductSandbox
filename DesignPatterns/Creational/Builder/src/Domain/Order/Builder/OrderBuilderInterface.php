<?php
declare(strict_types=1);

namespace App\Domain\Order\Builder;

use App\Domain\Common\Money;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\Value\Address;

interface OrderBuilderInterface
{
    public function start(string $orderId, string $customerEmail, string $currency): self;
    public function withShippingAddress(Address $address): self;
    public function withBillingAddress(Address $address): self;
    public function addItem(OrderItem $item): self;
    public function withShippingCost(Money $shipping): self;
    public function withCoupon(?string $code): self;
    public function withMeta(array $meta): self;

    /**
     * Tax/discount business rules are applied during assembly
     */
    public function build(): Order;
}
