<?php
declare(strict_types=1);

namespace App\Application\User\Command;

use App\Application\Mediator\Contracts\CommandHandlerInterface;
use App\Application\Mediator\Contracts\Command;
use App\Domain\User\UserRepository;
use App\Domain\User\User;

final class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $repo) {}

    /**
     * @param Command $command
     * @return int ID of a new user
     */
    public function handle(Command $command): int
    {
        \assert($command instanceof CreateUserCommand);
        $id = $this->repo->nextId();
        $user = new User($id, $command->email, $command->name);
        $this->repo->add($user);
        return $id;
    }
}
