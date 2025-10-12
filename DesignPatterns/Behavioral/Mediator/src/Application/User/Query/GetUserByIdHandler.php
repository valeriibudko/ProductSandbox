<?php
declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\Mediator\Contracts\QueryHandlerInterface;
use App\Domain\User\UserRepository;
use App\Application\Mediator\Contracts\Query;

final class GetUserByIdHandler implements QueryHandlerInterface
{
    public function __construct(private UserRepository $repo)
    {
    }

    public function handle(Query $query): ?array
    {
        \assert($query instanceof GetUserByIdQuery);
        $user = $this->repo->getById($query->id);
        return $user ? ['id' => $user->id, 'email' => $user->email, 'name' => $user->name] : null;
    }
}
