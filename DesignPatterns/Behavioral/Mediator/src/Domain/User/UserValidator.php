<?php
declare(strict_types=1);

namespace App\Domain\User;

final class UserValidator
{
    public function __construct(private UserRepository $repo) {}

    public function assertCreate(string $email, string $name): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        if ($this->repo->getByEmail($email)) {
            throw new \DomainException('Email already taken');
        }
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('Name too short');
        }
    }
}
