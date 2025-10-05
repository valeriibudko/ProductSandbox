#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Application\DTO\OrderCreateDTO;
use App\Application\Factory\OrderDirector;
use App\Domain\Order\Builder\OrderBuilder;

$payload = [
    'id' => 'ORD-100500',
    'email' => 'customer@example.com',
    'currency' => 'EUR',
    'items' => [
        ['sku' => 'SKU-1', 'name' => 'T-Shirt', 'qty' => 2, 'price' => 1999],
        ['sku' => 'SKU-2', 'name' => 'Jeans',  'qty' => 1, 'price' => 4999],
    ],
    'shipping' => [
        'country' => 'DE', 'city' => 'Berlin', 'line1' => 'Brunnenstrae 87', 'postal' => '13355'
    ],
    'billing' => [
        'country' => 'DE', 'city' => 'Berlin', 'line1' => 'Brunnenstrae 87', 'postal' => '13355'
    ],
    'shipping_cost' => 500, // 5.00 EUR
//    'coupon' => 'WELCOME10',
    'coupon' => '10',
    'meta' => ['source' => 'landing-hero'],
];

$dto = OrderCreateDTO::fromArray($payload);

$director = new OrderDirector(new OrderBuilder());
$order = $director->fromPayload($dto);

echo json_encode($order, JSON_PRETTY_PRINT) . PHP_EOL;
