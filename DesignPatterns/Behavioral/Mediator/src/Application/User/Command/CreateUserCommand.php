<?php
declare(strict_types=1);

namespace App\Application\User\Command;

use App\Application\Mediator\Contracts\Command;

final class CreateUserCommand implements Command
{
    public function __construct(
        public string $email,
        public string $name
    ) {}
}
