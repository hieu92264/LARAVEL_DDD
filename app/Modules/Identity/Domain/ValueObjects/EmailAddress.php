<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class EmailAddress
{
    private function __construct(private string $value)
    {

    }

    public static function fromString(string $email): self
    {
        $normalizedEmail = mb_strtolower(trim($email));

        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(__('identity::validation.email_address_invalid'));
        }

        return new self($normalizedEmail);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
