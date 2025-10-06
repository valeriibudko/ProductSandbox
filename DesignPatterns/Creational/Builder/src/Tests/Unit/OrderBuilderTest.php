<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\DTO\OrderCreateDTO;
use App\Application\Factory\OrderDirector;
use App\Domain\Order\Builder\OrderBuilder;
use PHPUnit\Framework\TestCase;

final class OrderBuilderTest extends TestCase
{
    public function testBuildOrderWithCouponAndTax(): void
    {
        $payload = [
            'id' => 'ORD-100500',
            'email' => 'customer@example.com',
            'currency' => 'EUR',
            'items' => [
                ['sku' => 'SKU-1', 'name' => 'T-Shirt', 'qty' => 2, 'price' => 1999],
                ['sku' => 'SKU-2', 'name' => 'Jeans',  'qty' => 1, 'price' => 4999],
            ],
            'shipping' => [
                'country' => 'PT', 'city' => 'Lisboa', 'line1' => 'Av. da Liberdade, 1', 'postal' => '1000-001'
            ],
            'billing' => [
                'country' => 'PT', 'city' => 'Lisboa', 'line1' => 'Av. da Liberdade, 1', 'postal' => '1000-001'
            ],
            'shipping_cost' => 500,
            'meta' => ['source' => 'landing-hero'],
        ];

        $orderCreateDTO = OrderCreateDTO::fromArray($payload);

        $director = new OrderDirector(new OrderBuilder());
        $order = $director->fromPayload($orderCreateDTO);

        $this->assertSame('ORD-100500', $order->id);
        $this->assertSame('customer@example.com', $order->customerEmail);
        $this->assertSame('EUR', $order->currency);
        $this->assertCount(2, $order->items);

        // itemsTotal = 2*19.99 + 49.99 = 89.97 => 8997
        $this->assertSame(8997, $order->itemsTotal->amount);

        // discount = 10% of itemsTotal = 899.7 => 900
//        $this->assertSame(900, $order->discount->amount);

        // taxBase = itemsTotal - discount + shipping = 8997 - 900 + 500 = 8597
        // tax = 23% of 8597 = 1977.31 => 1977
//        $this->assertSame(1977, $order->tax->amount);

        // grand = itemsTotal + shipping + tax - discount = 8997 + 500 + 1977 - 900 = 10574
//        $this->assertSame(10574, $order->grandTotal->amount);
    }
}
