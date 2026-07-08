<?php

namespace App\Modules\Identity\Application\DTOs;

use App\Modules\Identity\Domain\Entities\User;

final readonly class UserData
{
    public function __construct(
        public int    $id,
        public string $username,
        public string $email,
        public string $role,
        public bool   $isActive
    )
    {

    }

    public static function fromDomain(User $user): self
    {
        return new self(
            id: $user->idOrFail(),
            username: $user->username()->value(),
            email: $user->email()->value(),
            role: $user->role()->value,
            isActive: $user->isActive()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'isActive' => $this->isActive
        ];
    }
}
