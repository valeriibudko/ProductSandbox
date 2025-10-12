<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\User\{User, UserRepository};
use PDO;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function nextId(): int
    {
        $stmt = $this->pdo->query('SELECT COALESCE(MAX(id),0)+1 FROM users');
        return (int) $stmt->fetchColumn();
    }

    public function add(User $user): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (id, email, name) VALUES (:id, :email, :name)');
        $stmt->execute([':id'=>$user->id, ':email'=>$user->email, ':name'=>$user->name]);
    }

    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id,email,name FROM users WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User((int)$row['id'], $row['email'], $row['name']) : null;
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id,email,name FROM users WHERE email = :email');
        $stmt->execute([':email'=>$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User((int)$row['id'], $row['email'], $row['name']) : null;
    }
}
