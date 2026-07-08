<?php

namespace App\Modules\Identity\Domain\ValueObjects;

final readonly class PasswordHash
{
    private function __construct(private string $value)
    {

    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $passwordHash): self
    {
        if (trim($passwordHash) === '') {
            throw new \InvalidArgumentException(__('identity::validation.password_empty'));
        }

        return new self($passwordHash);
    }
}
