<?php

namespace App\Modules\Identity\Domain\Entities;

use App\Modules\Identity\Domain\Enums\UserRole;
use App\Modules\Identity\Domain\Exceptions\AccountInactive;
use App\Modules\Identity\Domain\ValueObjects\EmailAddress;
use App\Modules\Identity\Domain\ValueObjects\PasswordHash;
use App\Modules\Identity\Domain\ValueObjects\Username;
use LogicException;

final class User
{
    private function __construct(
        private ?int         $id,
        private Username     $username,
        private EmailAddress $email,
        private PasswordHash $passwordHash,
        private UserRole     $role,
        private bool         $isActive
    )
    {

    }

    public static function register(
        Username     $username,
        EmailAddress $email,
        PasswordHash $passwordHash,
        UserRole     $role = UserRole::USER
    ): self
    {
        return new self(
            id: null,
            username: $username,
            email: $email,
            passwordHash: $passwordHash,
            role: $role,
            isActive: true
        );
    }

    public static function reconstitute(
        int          $id,
        Username     $username,
        EmailAddress $email,
        PasswordHash $passwordHash,
        UserRole     $role,
        bool         $isActive
    ): self
    {
        return new self(
            id: $id,
            username: $username,
            email: $email,
            passwordHash: $passwordHash,
            role: $role,
            isActive: $isActive
        );
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function changeRole(UserRole $role): void
    {
        $this->role = $role;
    }

    public function ensureActive(): void
    {
        if (!$this->isActive) {
            throw new AccountInactive();
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function idOrFail(): int
    {
        return $this->id ?? throw new LogicException(__('identity::exception.user_not_save'));
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function email(): EmailAddress
    {
        return $this->email;
    }

    public function passwordHash(): PasswordHash
    {
        return $this->passwordHash;
    }

    public function role(): UserRole
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
