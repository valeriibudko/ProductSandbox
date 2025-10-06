<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\DTO\OrderCreateDTO;
use App\Application\Factory\OrderDirector;
use App\Domain\Order\Builder\OrderBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class OrderBuilderTest extends TestCase
{
    public static function additionProvider(): array
    {
        return [
            [
                [
                    'id' => 'ORD-100500',
                    'email' => 'customer@example.com',
                    'currency' => 'EUR',
                    'items' => [
                        ['sku' => 'SKU-1', 'name' => 'T-Shirt', 'qty' => 2, 'price' => 1999],
                        ['sku' => 'SKU-2', 'name' => 'Jeans', 'qty' => 1, 'price' => 4999],
                    ],
                    'shipping' => [
                        'country' => 'PT', 'city' => 'Lisbon', 'line1' => 'Av. da Liberdade, 1', 'postal' => '1000-001'
                    ],
                    'billing' => [
                        'country' => 'PT', 'city' => 'Lisbon', 'line1' => 'Av. da Liberdade, 1', 'postal' => '1000-001'
                    ],
                    'shipping_cost' => 500,
                    'coupon' => 'WELCOME10',
                    'meta' => ['source' => 'landing-hero'],
                ],
                [
                    'id' => 'ORD-100500',
                    'currency' => 'EUR',
                    'itemsTotal' => 8997,  // itemsTotal = 2*19.99 + 49.99 = 89.97 => 8997
                    'discount' => 900, // discount = 10% of itemsTotal = 899.7 => 900
                    // taxBase = itemsTotal - discount + shipping = 8997 - 900 + 500 = 8597
                    // tax = 23% of 8597 = 1977.31 => 1977
                    'taxBase' => 1977,
                    'grandTotal' => 10574, // grand = itemsTotal + shipping + tax - discount = 8997 + 500 + 1977 - 900 = 10574
                ]
            ],
            [
                [
                    'id' => 'ORD-100501',
                    'email' => 'customer@example.com',
                    'currency' => 'EUR',
                    'items' => [
                        ['sku' => 'SKU-12', 'name' => 'Jeans', 'qty' => 1, 'price' => 3000],
                        ['sku' => 'SKU-23', 'name' => 'Jeans', 'qty' => 1, 'price' => 4000],
                    ],
                    'shipping' => [
                        'country' => 'DE', 'city' => 'Berlin', 'line1' => 'Brunnenstrae 87', 'postal' => '13355'
                    ],
                    'billing' => [
                        'country' => 'DE', 'city' => 'Berlin', 'line1' => 'Brunnenstrae 87', 'postal' => '13355'
                    ],
                    'shipping_cost' => 400,
                    'coupon' => 'WELCOME10',
                    'meta' => ['source' => 'landing-hero'],
                ],
                [
                    'id' => 'ORD-100501',
                    'currency' => 'EUR',
                    'itemsTotal' => 7000,
                    'discount' => 700,
                    'taxBase' => 1541,
                    'grandTotal' => 8241,
                ]
            ],

        ];
    }


    #[DataProvider('additionProvider')]
    public function testBuildOrderWithCouponAndTax(array $input, array $expected): void
    {
        $orderCreateDTO = OrderCreateDTO::fromArray($input);

        $director = new OrderDirector(new OrderBuilder());
        $order = $director->fromPayload($orderCreateDTO);

        $this->assertSame($expected['id'], $order->id);
        $this->assertSame($expected['currency'], $order->currency);

        $this->assertSame($expected['itemsTotal'], $order->itemsTotal->amount);
        $this->assertSame($expected['discount'], $order->discount->amount);
        $this->assertSame($expected['taxBase'], $order->tax->amount);
        $this->assertSame($expected['grandTotal'], $order->grandTotal->amount);
    }
}
