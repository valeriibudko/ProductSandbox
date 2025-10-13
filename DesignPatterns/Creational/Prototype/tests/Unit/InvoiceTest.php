<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Billing\Exception\DomainException;
use App\Billing\Invoice\Invoice;
use App\Billing\Invoice\LineItem;
use App\Billing\Value\Address;
use App\Billing\Value\Money;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    private Address $addressSeller;
    private Address $addressBuyer;

    protected function setUp(): void
    {
        $this->addressSeller  = new Address(
            company: 'ACME LTD',
            line1: 'Main St 1',
            line2: null,
            city: 'Lisbon',
            country: 'PT',
            postal:'1000-001');

        $this->addressBuyer = new Address(
            company: 'Contoso LLC',
            line1: '2nd Ave 9',
            line2: 'Suite 5',
            city: 'Porto',
            country: 'PT',
            postal: '4000-123'
        );
    }

    private function makeInvoice(): Invoice
    {
        $invoice = new Invoice(
            id: 'db-uuid-1',
            number: 'TPL-ACME-CONTOSO',
            seller: $this->addressSeller,
            buyer: $this->addressBuyer,
            currency: 'EUR',
            issuedAt: new DateTimeImmutable('2025-09-01'),
            dueAt: (new DateTimeImmutable('2025-09-01'))->add(new DateInterval('P14D')),
            comment: 'Retainer plan — 20h',
            meta: ['template' => true, 'tax' => 'PT-23%'],
        );

        $invoice->addItem(new LineItem('SVC-RET-20', 'Monthly retainer, 20h', 1, new Money(200000, 'EUR')));
        $invoice->addItem(new LineItem('SVC-SUPPORT', 'Priority support', 1, new Money(30000, 'EUR')));

        return $invoice;
    }

    public function testTotalCalculation(): void
    {
        $invoice = $this->makeInvoice();

        $arr = $invoice->toArray();
        $this->assertSame(230000, $arr['total']);
        $this->assertSame('EUR', $arr['currency']);
        $this->assertSame('2025-09-01', $arr['issuedAt']);
        $this->assertSame('2025-09-15', $arr['dueAt']);
        $this->assertCount(2, $arr['items']);
    }

    public function testAddItemCurrencyMismatchThrows(): void
    {
        $invoice = $this->makeInvoice();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Item currency mismatch');

        $invoice->addItem(new LineItem('USD-1', 'Foreign item', 1, new Money(100, 'USD')));
    }

    public function testCloneResetsIdAndDeepCopiesItems(): void
    {
        $invoice = $this->makeInvoice();
        $clone = clone $invoice;

        // ID reset
        $orig = $invoice->toArray();
        $copy = $clone->toArray();
        $this->assertSame('db-uuid-1', $orig['id']);
        $this->assertNull($copy['id']);

        // changing the clone instance does not affect the original (array of positions deep copy)
        $this->assertSame(230000, $orig['total']);
        $this->assertSame(230000, $copy['total']);

        $clone->addItem(new LineItem('EXTRA', 'Extra service', 1, new Money(10000, 'EUR')));
        $copyAfter = $clone->toArray();

        $this->assertSame(230000, $orig['total']);     // original is change
        $this->assertSame(240000, $copyAfter['total']); // clone is change

        // meta deep copy via replacement
        $clone2 = $clone->withMeta(['template' => false, 'note' => 'changed']);
        $this->assertSame(['template' => true, 'tax' => 'PT-23%'], $invoice->toArray()['meta']);
        $this->assertSame(['template' => false, 'note' => 'changed'], $clone2->toArray()['meta']);
    }

    public function testWithersReturnNewInstances(): void
    {
        $invoice = $this->makeInvoice();

        $newNumber = 'INV-2025-10-0001';
        $issued = new DateTimeImmutable('2025-10-01');
        $due = $issued->add(new DateInterval('P14D'));

        $invoice2 = $invoice->withNumber($newNumber)->withIssueDates($issued, $due)->withComment('ok');

        $this->assertNotSame($invoice, $invoice2);
        $this->assertSame('TPL-ACME-CONTOSO', $invoice->toArray()['number']);
        $this->assertSame($newNumber, $invoice2->toArray()['number']);
        $this->assertSame('2025-10-01', $invoice2->toArray()['issuedAt']);
        $this->assertSame('2025-10-15', $invoice2->toArray()['dueAt']);
        $this->assertSame('ok', $invoice2->toArray()['comment']);
    }
}
