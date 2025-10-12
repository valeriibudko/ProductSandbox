<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    public function nextId(): int;
    public function add(User $user): void;
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;
}
