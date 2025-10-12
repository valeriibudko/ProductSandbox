<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\User\User;
use App\Domain\User\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<int,User> */
    private array $items = [];
    private int $seq = 1;

    public function nextId(): int { return $this->seq++; }

    public function add(User $user): void { $this->items[$user->id] = $user; }

    public function getById(int $id): ?User { return $this->items[$id] ?? null; }

    public function getByEmail(string $email): ?User {
        foreach ($this->items as $u) {
            if (strcasecmp($u->email, $email) === 0) { return $u; }
        }
        return null;
    }
}
