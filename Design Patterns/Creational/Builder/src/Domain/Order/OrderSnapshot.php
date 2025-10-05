<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Order\Value\Address;

/**
 * Internal immutable snapshot for passing state from the builder to the Order.
 * Additional benefit: convenient serialization/logging.
 */
final class OrderSnapshot
{
    /** @param list<OrderItem> $items */
    public function __construct(
        public readonly string $id,
        public readonly string $customerEmail,
        public readonly array $items,
        public readonly Address $shippingAddress,
        public readonly Address $billingAddress,
        public readonly Money $itemsTotal,
        public readonly Money $shippingCost,
        public readonly Money $discount,
        public readonly Money $tax,
        public readonly Money $grandTotal,
        public readonly string $currency,
        public readonly ?string $coupon,
        public readonly array $meta
    ) {}
}
