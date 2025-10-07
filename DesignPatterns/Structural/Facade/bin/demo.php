<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Application\CheckoutFacade;
use App\Domain\Address;
use App\Domain\Customer;
use App\Domain\Money;
use App\Domain\Order;
use App\Domain\OrderItem;
use App\Infrastructure\Inventory\InventoryService;
use App\Infrastructure\Invoice\InvoiceService;
use App\Infrastructure\PaymentGateway\PaymentGateway;
use App\Infrastructure\FraudChecker\FraudChecker;
use App\Infrastructure\Shipping\ShippingService;
use App\Infrastructure\StdoutLogger\StdoutStdoutLogger;

$customer = new Customer(id: 'cust_123', email: 'alice@example.com', fullName: 'Alice Doe');
$address  = new Address(line1: 'Main St 1', line2: null, city: 'Lisbon', postalCode: '1000-001', countryCode: 'PT');

$items = [
    new OrderItem(sku: 'SKU-RED-MUG', qty: 2, pricePerUnit: new Money(1299, 'EUR')),
    new OrderItem(sku: 'SKU-BLUE-TEE', qty: 1, pricePerUnit: new Money(2499, 'EUR')),
];

$order = new Order(
    id: 'ord_'.bin2hex(random_bytes(3)),
    customer: $customer,
    shippingAddress: $address,
    items: $items
);

$facade = new CheckoutFacade(
    payments:  new PaymentGateway(),
    inventory: new InventoryService(),
    shipping:  new ShippingService(),
    invoices:  new InvoiceService(),
    fraud:     new FraudChecker(),
    logger:    new StdoutStdoutLogger()
);

try {
    $result = $facade->checkout($order);

    echo PHP_EOL."=== CHECKOUT RESULT ===".PHP_EOL;
    echo "Order:     {$result->orderId}".PHP_EOL;
    echo "Payment:   {$result->paymentId}".PHP_EOL;
    echo "Invoice:   {$result->invoiceId} ({$result->invoiceUrl})".PHP_EOL;
    echo "Shipment:  {$result->shipmentId}".PHP_EOL;
    echo "Tracking:  {$result->trackingNumber}".PHP_EOL;
    echo "Reserve:   {$result->reservationId}".PHP_EOL;
} catch (Throwable $e) {
    echo PHP_EOL."Checkout error: ".$e->getMessage().PHP_EOL;
}
