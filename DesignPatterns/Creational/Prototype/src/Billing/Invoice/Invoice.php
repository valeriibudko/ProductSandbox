<?php
declare(strict_types=1);

namespace App\Billing\Invoice;

use App\Billing\Exception\DomainException;
use App\Billing\Value\Address;
use App\Billing\Value\Money;
use DateTimeImmutable;

final class Invoice
{
    /** @var list<LineItem> */
    private array $items = [];

    public function __construct(
        private ?string $id, // null for new object
        private string $number,
        private Address $seller,
        private Address $buyer,
        private string $currency,
        private DateTimeImmutable $issuedAt,
        private DateTimeImmutable $dueAt,
        private ?string $comment = null,
        private array $meta = []
    ) {}

    public function addItem(LineItem $item): void
    {
        if ($item->unitPrice->currency !== $this->currency) {
            throw new DomainException('Item currency mismatch');
        }
        $this->items[] = $item;
    }

    /**
     * Final bill with all job items
     */
    public function total(): Money
    {
        $sum = Money::zero($this->currency);
        foreach ($this->items as $i) {
            $sum = $sum->add($i->subtotal());
        }
        return $sum;
    }

    /**
     * For modification after cloning
     * @param string $number
     * @return $this
     */
    public function withNumber(string $number): self
    {
        $clone = clone $this;
        $clone->number = $number;
        return $clone;
    }

    public function withIssueDates(DateTimeImmutable $issued, DateTimeImmutable $due): self
    {
        $clone = clone $this;
        $clone->issuedAt = $issued;
        $clone->dueAt = $due;
        return $clone;
    }

    public function withComment(?string $comment): self
    {
        $clone = clone $this;
        $clone->comment = $comment;
        return $clone;
    }

    public function withMeta(array $meta): self
    {
        $clone = clone $this;
        $clone->meta = $meta;
        return $clone;
    }

    /**
     * Return a public snapshot. Safe for serialisation and render.
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'number'    => $this->number,
            'seller'    => get_object_vars($this->seller),
            'buyer'     => get_object_vars($this->buyer),
            'currency'  => $this->currency,
            'issuedAt'  => $this->issuedAt->format('Y-m-d'),
            'dueAt'     => $this->dueAt->format('Y-m-d'),
            'comment'   => $this->comment,
            'meta'      => $this->meta,
            'items'     => array_map(
                fn(LineItem $i) => [
                    'sku'   => $i->sku,
                    'name'  => $i->name,
                    'qty'   => $i->qty,
                    'price' => $i->unitPrice->amount,
                ],
                $this->items
            ),
            'total'     => $this->total()->amount,
        ];
    }

    /**
     * A core of prototype. Deep cloning
     * @return void
     */
    public function __clone(): void
    {
        // Reset field for new record
        $this->id = null;

        // Addresses are immutable value objects (copied can be skip)
        $this->seller = new Address(...get_object_vars($this->seller));
        $this->buyer  = new Address(...get_object_vars($this->buyer));

        // Items: deep cloning each item
        $items = [];
        foreach ($this->items as $item) {
            $items[] = clone $item; // LineItem::__clone()
        }
        $this->items = $items;

        // Dates are immutable (DateTimeImmutable), they can be left as is
        //// Meta is array: we copy it to avoid splitting the reference
        $this->meta = $this->meta ? unserialize(serialize($this->meta)) : [];
    }
}
