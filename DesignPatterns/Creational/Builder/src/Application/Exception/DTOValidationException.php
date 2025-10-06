<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class DTOValidationException extends RuntimeException
{
    /** @param list<string> $errors */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('DTO validation failed: ' . implode('; ', $errors));
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        $errors = [];
        foreach ($violations as $v) {
            $errors[] = sprintf('%s: %s', (string)$v->getPropertyPath(), (string)$v->getMessage());
        }
        return new self($errors);
    }

    /** @return list<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
