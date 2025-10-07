<?php
declare(strict_types=1);

namespace App\Application;

use App\Application\DTO\CheckoutErrorDTO;
use App\Application\DTO\CheckoutResultDTO;
use App\Domain\Order;
use App\Infrastructure\Inventory\InventoryServiceInterface;
use App\Infrastructure\Invoice\InvoiceServiceInterface;
use App\Infrastructure\FraudChecker\FraudCheckerInterface;
use App\Infrastructure\PaymentGateway\PaymentGatewayInterface;
use App\Infrastructure\Shipping\ShippingServiceInterface;
use App\Infrastructure\StdoutLogger\StdoutLoggerInterface;

final class CheckoutFacade
{
    public function __construct(
        private readonly PaymentGatewayInterface       $payments,
        private readonly InventoryServiceInterface $inventory,
        private readonly ShippingServiceInterface      $shipping,
        private readonly InvoiceServiceInterface   $invoices,
        private readonly FraudCheckerInterface         $fraud,
        private readonly StdoutLoggerInterface $logger
    ) {}

    public function checkout(Order $order): CheckoutResultDTO
    {
        $this->logger->info('Checkout started', ['orderId' => $order->id, 'total' => $order->total()->amountMinor]);

        try {
            // Antifraud
            $this->fraud->check($order);
            $this->logger->info('Fraud check passed', ['orderId' => $order->id]);

            // Reserve in warehouse
            $reservation = $this->inventory->reserve($order);
            $this->logger->info('Inventory reserved', $reservation + ['orderId' => $order->id]);

            // Payment write-off
            $payment = $this->payments->charge($order->customer->id, $order->total());
            if (!$payment['captured']) {
                throw new CheckoutErrorDTO('Payment not captured.');
            }
            $this->logger->info('Payment captured', $payment + ['orderId' => $order->id]);

            // Invoice
            $invoice = $this->invoices->issueInvoice($order, $payment['paymentId']);
            $this->logger->info('Invoice issued', $invoice + ['orderId' => $order->id]);

            // Delivery
            $shipment = $this->shipping->createShipment($order);
            $this->logger->info('Shipment created', $shipment + ['orderId' => $order->id]);

            return new CheckoutResultDTO(
                orderId: $order->id,
                paymentId: $payment['paymentId'],
                invoiceId: $invoice['invoiceId'],
                invoiceUrl: $invoice['url'],
                shipmentId: $shipment['shipmentId'],
                trackingNumber: $shipment['trackingNumber'],
                reservationId: $reservation['reservationId']
            );
        } catch (\Throwable $e) {
            $this->logger->error('Checkout failed', [
                'orderId' => $order->id,
                'error' => $e->getMessage()
            ]);
            // TODO Compensations: cancel the reservation, refund the payment, etc.
            throw new CheckoutErrorDTO($e->getMessage(), previous: $e);
        }
    }
}
