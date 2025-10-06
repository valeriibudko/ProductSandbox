<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Common\Money;
use App\Domain\Common\ValidationException;

final class OrderItem
{
    public readonly string $sku;
    public readonly string $name;
    public readonly int $qty;
    public readonly Money $price; // price per unit

    private function __construct(string $sku, string $name, int $qty, Money $price)
    {
        $errors = [];
        if ($sku === '') $errors[] = 'SKU is required';
        if ($name === '') $errors[] = 'Name is required';
        if ($qty <= 0) $errors[] = 'Quantity must be > 0';
        if ($errors) throw ValidationException::withMessages($errors);

        $this->sku = $sku;
        $this->name = $name;
        $this->qty = $qty;
        $this->price = $price;
    }

    public static function of(string $sku, string $name, int $qty, Money $price): self
    {
        return new self($sku, $name, $qty, $price);
    }

    public function subtotal(): Money
    {
        return $this->price->multiply($this->qty);
    }
}
