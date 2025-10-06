<?php

declare(strict_types=1);

namespace App\Domain\Order\Builder;

use App\Domain\Common\Money;
use App\Domain\Common\ValidationException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderSnapshot;
use App\Domain\Order\Value\Address;

final class OrderBuilder implements OrderBuilderInterface
{
    private array $couponDiscountList = [
        'WELCOME10' => 0.10
    ];
    private ?string $id = null;
    private ?string $email = null;
    private ?string $currency = null;

    /** @var list<OrderItem> */
    private array $items = [];
    private ?Address $shipping = null;
    private ?Address $billing = null;
    private ?Money $shippingCost = null;
    private ?string $coupon = null;
    private ?float $couponDiscount = null;
    private array $meta = [];

    public function start(string $orderId, string $customerEmail, string $currency): self
    {
        $this->id = $orderId;
        $this->email = $customerEmail;
        $this->currency = strtoupper($currency);
        return $this;
    }

    public function withShippingAddress(Address $address): self
    {
        $this->shipping = $address;
        return $this;
    }

    public function withBillingAddress(Address $address): self
    {
        $this->billing = $address;
        return $this;
    }

    public function addItem(OrderItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function withShippingCost(Money $shipping): self
    {
        $this->shippingCost = $shipping;
        return $this;
    }

    public function withCoupon(?string $code): self
    {
        $this->coupon = $code ? strtoupper(trim($code)) : null;
        if($this->coupon) {
            $this->couponDiscount = $this->couponDiscountList[$this->coupon];
            }
        return $this;
    }

    public function withMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function build(): Order
    {
        $errors = [];

        if ($this->id === null) $errors[] = 'Call start() with order ID';
        if ($this->email === null) $errors[] = 'Call start() with customer email';
        if ($this->currency === null) $errors[] = 'Call start() with currency';
        if ($this->shipping === null) $errors[] = 'Shipping address required';
        if ($this->billing === null) $errors[] = 'Billing address required';
        if ($this->items === []) $errors[] = 'At least one item required';

        // Cash settlements, single currency
        $currency = $this->currency ?? Money::CURRENCY_EUR;
        $itemsTotal = Money::of(0, $currency);
        foreach ($this->items as $i) {
            if ($i->price->currency !== $currency) {
                $errors[] = 'Item currency mismatch with order';
            }
            $itemsTotal = $itemsTotal->add($i->subtotal());
        }

        $shipping = $this->shippingCost ?? Money::of(0, $currency);
        if ($shipping->currency !== $currency) {
            $errors[] = 'Shipping cost currency mismatch';
        }

        $taxBase = $itemsTotal->add($shipping);
        if ($this->couponDiscount) {
            $discount = $itemsTotal->multiply($this->couponDiscount);
            $taxBase = $taxBase->subtract(Money::of($discount->amount, $currency));
        }

        // Tax 23% from (itemsTotal - discount + shipping)
        $tax = $taxBase->multiply(0.23);

        // Summary
        $grand = $itemsTotal
            ->add($shipping)
            ->add($tax)
            ->subtract(Money::of($discount->amount, $currency));

        if ($grand->amount < 0) {
            $errors[] = 'Grand total is negative, check discounts';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $snap = new OrderSnapshot(
            id: $this->id,
            customerEmail: $this->email,
            items: $this->items,
            shippingAddress: $this->shipping,
            billingAddress: $this->billing,
            itemsTotal: $itemsTotal,
            shippingCost: $shipping,
            discount: $discount,
            tax: $tax,
            grandTotal: $grand,
            currency: $currency,
            coupon: $this->coupon,
            meta: $this->meta
        );

        // Reset builder. Safe for reuse
        $this->reset();

        return Order::fromBuilder($snap);
    }

    private function reset(): void
    {
        $this->id = null;
        $this->email = null;
        $this->currency = null;
        $this->items = [];
        $this->shipping = null;
        $this->billing = null;
        $this->shippingCost = null;
        $this->coupon = null;
        $this->meta = [];
    }
}
