<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Application\Exception\DTOValidationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class OrderCreateDTO
{
    /** @var list<OrderItemDto> */
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public readonly array $items;

    public function __construct(
        #[Assert\NotBlank] #[Assert\Email] public readonly string $email,
        #[Assert\NotBlank] public readonly string $id,
        #[Assert\NotBlank] #[Assert\Currency] public readonly string $currency,
        array $items,
        #[Assert\Valid] public readonly AddressDto $shipping,
        #[Assert\Valid] public readonly AddressDto $billing,
        #[Assert\PositiveOrZero] public readonly ?int $shippingCost = null,
        public readonly ?string $coupon = null,
        /** @var array<string,mixed> */
        public readonly array $meta = []
    ) {
        $this->items = $items;
    }

    /**
     * @param array{
     *   id:string, email:string, currency:string,
     *   items:list<array{sku:string,name:string,qty:int,price:int}>,
     *   shipping:array{country:string,city:string,line1:string,line2?:string,postal:string},
     *   billing:array{country:string,city:string,line1:string,line2?:string,postal:string},
     *   shipping_cost?:int,
     *   coupon?:string,
     *   meta?:array<string,mixed>
     * } $payload
     */
    public static function fromArray(array $payload): self
    {
        $items = array_map(
            fn(array $it) => OrderItemDto::fromArray($it),
            (array)($payload['items'] ?? [])
        );

        return new self(
            email: (string)($payload['email'] ?? ''),
            id: (string)($payload['id'] ?? ''),
            currency: strtoupper((string)($payload['currency'] ?? '')),
            items: $items,
            shipping: AddressDto::fromArray((array)($payload['shipping'] ?? [])),
            billing: AddressDto::fromArray((array)($payload['billing'] ?? [])),
            shippingCost: array_key_exists('shipping_cost', $payload) ? (int)$payload['shipping_cost'] : null,
            coupon: isset($payload['coupon']) && $payload['coupon'] !== '' ? (string)$payload['coupon'] : null,
            meta: (array)($payload['meta'] ?? [])
        );
    }

    public static function fromArrayValidated(array $payload, ?ValidatorInterface $validator = null): self
    {
        $dto = self::fromArray($payload);
        $dto->assertValid($validator);
        return $dto;
    }

    public function validate(?ValidatorInterface $validator = null): ConstraintViolationListInterface
    {
        $validator ??= Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        return $validator->validate($this);
    }

    public function assertValid(?ValidatorInterface $validator = null): void
    {
        $violations = $this->validate($validator);
        if (\count($violations) > 0) {
            throw DTOValidationException::fromViolations($violations);
        }
    }
}
