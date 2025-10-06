<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Order\Value\Address;
use App\Domain\Common\ValidationException;

final class Order
{
    public readonly string $id;
    public readonly string $customerEmail;
    /** @var list<OrderItem> */
    public readonly array $items;
    public readonly Address $shippingAddress;
    public readonly Address $billingAddress;
    public readonly Money $itemsTotal;
    public readonly Money $shippingCost;
    public readonly Money $discount;
    public readonly Money $tax;
    public readonly Money $grandTotal;
    public readonly string $currency;
    public readonly ?string $coupon;
    public readonly array $meta;

    /**
     * @param list<OrderItem> $items
     */
    private function __construct(
        string $id,
        string $customerEmail,
        array $items,
        Address $shippingAddress,
        Address $billingAddress,
        Money $itemsTotal,
        Money $shippingCost,
        Money $discount,
        Money $tax,
        Money $grandTotal,
        string $currency,
        ?string $coupon,
        array $meta
    ) {
        $errors = [];
        if ($id === '') $errors[] = 'Order ID is required';
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Customer email invalid';
        if ($items === []) $errors[] = 'Order must contain at least one item';
        if ($errors) throw ValidationException::withMessages($errors);

        $this->id = $id;
        $this->customerEmail = strtolower($customerEmail);
        $this->items = $items;
        $this->shippingAddress = $shippingAddress;
        $this->billingAddress = $billingAddress;
        $this->itemsTotal = $itemsTotal;
        $this->shippingCost = $shippingCost;
        $this->discount = $discount;
        $this->tax = $tax;
        $this->grandTotal = $grandTotal;
        $this->currency = $currency;
        $this->coupon = $coupon;
        $this->meta = $meta;
    }

    /**
     * Only the builder is responsible for assembling the correct state
     */
    public static function fromBuilder(OrderSnapshot $snap): self
    {
        return new self(
            $snap->id,
            $snap->customerEmail,
            $snap->items,
            $snap->shippingAddress,
            $snap->billingAddress,
            $snap->itemsTotal,
            $snap->shippingCost,
            $snap->discount,
            $snap->tax,
            $snap->grandTotal,
            $snap->currency,
            $snap->coupon,
            $snap->meta
        );
    }
}
