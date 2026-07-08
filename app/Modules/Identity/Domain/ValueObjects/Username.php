<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Username
{
    private function __construct(private string $value)
    {

    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $username): self
    {
        $normalizedUsername = mb_strtolower(trim($username));

        $length = mb_strlen($normalizedUsername);

        if ($length < 3 || $length > 50) {
            throw new InvalidArgumentException(__('identity::validation.username_invalid'));
        }

        if (!preg_match(
            '/^[a-z0-9._-]+$/',
            $normalizedUsername,
        )) {
            throw new InvalidArgumentException(__('identity::validation.username_invalid_characters'),
            );
        }

        return new self($normalizedUsername);
    }
}
