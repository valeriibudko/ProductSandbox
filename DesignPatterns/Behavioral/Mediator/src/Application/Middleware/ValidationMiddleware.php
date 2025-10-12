<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Mediator\Contracts\MiddlewareInterface;
use App\Domain\User\UserValidator;
use App\Application\User\Command\CreateUserCommand;

final class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private UserValidator $userValidator) {}

    /**
     * Validate message for command
     *
     * @param object $message
     * @param callable $next
     * @return mixed
     */
    public function process(object $message, callable $next): mixed
    {
        // TODO Implement multi validation for different commands
        if ($message instanceof CreateUserCommand) {
            $this->userValidator->assertCreate($message->email, $message->name);
        }
        return $next($message);
    }
}
