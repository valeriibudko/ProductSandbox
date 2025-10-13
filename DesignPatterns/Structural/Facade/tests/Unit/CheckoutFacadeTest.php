<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\CheckoutFacade;
use App\Application\DTO\CheckoutErrorDTO;
use App\Domain\{Address, Customer, Money, Order, OrderItem};
use App\Infrastructure\FraudChecker\{FraudCheckerInterface};
use App\Infrastructure\Inventory\InventoryServiceInterface;
use App\Infrastructure\Invoice\InvoiceServiceInterface;
use App\Infrastructure\PaymentGateway\PaymentGatewayInterface;
use App\Infrastructure\Shipping\ShippingServiceInterface;
use App\Infrastructure\StdoutLogger\StdoutLoggerInterface;
use PHPUnit\Framework\TestCase;

final class CheckoutFacadeTest extends TestCase
{
    private function makeOrder(): Order
    {
        $customer = new Customer(id: 'cust_1', email: 'u@example.com', fullName: 'User Example');
        $address = new Address(line1: 'Main 1', line2: null, city: 'Lisbon', postalCode: '1000-001', countryCode: 'PT');

        $items = [
            new OrderItem(sku: 'SKU-1', qty: 2, pricePerUnit: new Money(1500, 'EUR')),
            new OrderItem(sku: 'SKU-2', qty: 1, pricePerUnit: new Money(2000, 'EUR')),
        ];
        // total = 2*1500 + 1*2000 = 5000 minor units (50.00 EUR)
        return new Order(id: 'ord_123', customer: $customer, shippingAddress: $address, items: $items);
    }

    public function testCheckoutSuccess(): void
    {
        $order = $this->makeOrder();

        $payments = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryServiceInterface::class);
        $shipping = $this->createMock(ShippingServiceInterface::class);
        $invoices = $this->createMock(InvoiceServiceInterface::class);
        $fraud = $this->createMock(FraudCheckerInterface::class);
        $logger = $this->createMock(StdoutLoggerInterface::class);

        // Fraud passes (no exception)
        $fraud->expects($this->once())
            ->method('check')
            ->with($order);

        // Inventory reserve returns reservationId
        $inventory->expects($this->once())
            ->method('reserve')
            ->with($order)
            ->willReturn(['reservationId' => 'res_abc']);

        // Payment captured
        $payments->expects($this->once())
            ->method('charge')
            ->with($order->customer->id, $this->callback(function (Money $m) {
                return $m->amountMinor === 5000 && $m->currency === 'EUR';
            }))
            ->willReturn(['paymentId' => 'pay_abc', 'captured' => true]);

        // Invoice issued
        $invoices->expects($this->once())
            ->method('issueInvoice')
            ->with($order, 'pay_abc')
            ->willReturn(['invoiceId' => 'inv_abc', 'url' => 'https://invoices.local/pay_abc']);

        // Shipment created
        $shipping->expects($this->once())
            ->method('createShipment')
            ->with($order)
            ->willReturn(['shipmentId' => 'shp_abc', 'trackingNumber' => 'TRK1234567']);

        // Logger optional: check that at least info() is called multiple times
        $logger->expects($this->atLeast(1))
            ->method('info');

        $facade = new CheckoutFacade(
            payments: $payments,
            inventory: $inventory,
            shipping: $shipping,
            invoices: $invoices,
            fraud: $fraud,
            logger: $logger
        );

        $result = $facade->checkout($order);

        $this->assertSame('ord_123', $result->orderId);
        $this->assertSame('pay_abc', $result->paymentId);
        $this->assertSame('inv_abc', $result->invoiceId);
        $this->assertSame('https://invoices.local/pay_abc', $result->invoiceUrl);
        $this->assertSame('shp_abc', $result->shipmentId);
        $this->assertSame('TRK1234567', $result->trackingNumber);
        $this->assertSame('res_abc', $result->reservationId);
    }

    public function testCheckoutFailsOnFraud(): void
    {
        $order = $this->makeOrder();

        $payments = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryServiceInterface::class);
        $shipping = $this->createMock(ShippingServiceInterface::class);
        $invoices = $this->createMock(InvoiceServiceInterface::class);
        $fraud = $this->createMock(FraudCheckerInterface::class);
        $logger = $this->createMock(StdoutLoggerInterface::class);

        $fraud->expects($this->once())
            ->method('check')
            ->with($order)
            ->willThrowException(new \RuntimeException('High-risk amount. Manual review required.'));

        // Other services should not be called.
        $inventory->expects($this->never())->method('reserve');
        $payments->expects($this->never())->method('charge');
        $invoices->expects($this->never())->method('issueInvoice');
        $shipping->expects($this->never())->method('createShipment');

        // Expect the facade to wrap in CheckoutErrorDTO
        $logger->expects($this->once())->method('error');

        $facade = new CheckoutFacade($payments, $inventory, $shipping, $invoices, $fraud, $logger);

        $this->expectException(CheckoutErrorDTO::class);
        $this->expectExceptionMessage('High-risk amount. Manual review required.');

        $facade->checkout($order);
    }

    public function testCheckoutFailsWhenPaymentNotCaptured(): void
    {
        $order = $this->makeOrder();

        $payments = $this->createMock(PaymentGatewayInterface::class);
        $inventory = $this->createMock(InventoryServiceInterface::class);
        $shipping = $this->createMock(ShippingServiceInterface::class);
        $invoices = $this->createMock(InvoiceServiceInterface::class);
        $fraud = $this->createMock(FraudCheckerInterface::class);
        $logger = $this->createMock(StdoutLoggerInterface::class);

        // Fraud ok
        $fraud->expects($this->once())
            ->method('check')
            ->with($order);

        // Inventory reserved ok
        $inventory->expects($this->once())
            ->method('reserve')
            ->with($order)
            ->willReturn(['reservationId' => 'res_ok']);

        // Payment not captured
        $payments->expects($this->once())
            ->method('charge')
            ->with($order->customer->id, $this->callback(function (Money $m) {
                return $m->amountMinor === 5000 && $m->currency === 'EUR';
            }))
            ->willReturn(['paymentId' => 'pay_fail', 'captured' => false]);

        $invoices->expects($this->never())->method('issueInvoice');
        $shipping->expects($this->never())->method('createShipment');

        $logger->expects($this->once())->method('error');

        $facade = new CheckoutFacade($payments, $inventory, $shipping, $invoices, $fraud, $logger);

        $this->expectException(CheckoutErrorDTO::class);
        $this->expectExceptionMessage('Payment not captured.');

        $facade->checkout($order);
    }
}
