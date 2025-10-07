<?php

declare(strict_types=1);

namespace App\Domain;

final class Customer
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $fullName
    ) {}
}