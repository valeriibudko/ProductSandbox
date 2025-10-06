#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Billing\Invoice\Invoice;
use App\Billing\Invoice\LineItem;
use App\Billing\Prototype\InvoicePrototypeRegistry;
use App\Billing\Value\Address;
use App\Billing\Value\Money;
use App\Billing\Service\RecurringInvoiceService;

$registry = new InvoicePrototypeRegistry();

$addressSeller = new Address(
        company: 'ACME LTD',
        line1: 'Main St 1',
        line2: null,
        city: 'Lisbon',
        country: 'PT',
        postal:'1000-001');

$addressBuyer = new Address(
        company: 'Contoso LLC',
        line1: '2nd Ave 9',
        line2: 'Suite 5',
        city: 'Porto',
        country: 'PT',
        postal: '4000-123'
);

$invoiceAugust = new Invoice(
        id: 'db-uuid-1',
        number: 'TPL-ACME-CONTOSO',
        seller: $addressSeller,
        buyer: $addressBuyer,
        currency: 'EUR',
        issuedAt: new DateTimeImmutable('2025-09-01'),
        dueAt: new DateTimeImmutable('2025-09-15'),
        comment: 'Retainer plan — 20h',
        meta: ['template' => true, 'tax' => 'PT-23%'],
);

$invoiceAugust->addItem(
        new LineItem(
                sku: 'SVC-RET-20',
                name: 'Monthly retainer, 20h',
                qty:1,
                unitPrice: new Money(200000, 'EUR')
        )
);

$invoiceAugust->addItem(
        new LineItem(
                sku:'SVC-SUPPORT',
                name:'Priority support',
                qty:1,
                unitPrice: new Money(30000, 'EUR')
        )
);

$registry->register('contoso_monthly', $invoiceAugust);

// Now we can create clone and make a bill for next month
$service = new RecurringInvoiceService($registry);
$new = $service->createFromPrototype(
        prototypeKey: 'contoso_monthly',
        newNumber: 'INV-2025-10-0001',
        issuedAt: new DateTimeImmutable('2025-10-01')
);

print_r($new->toArray());
