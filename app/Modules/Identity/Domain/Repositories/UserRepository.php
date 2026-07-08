<?php

namespace App\Modules\Identity\Domain\Repositories;

use App\Modules\Identity\Domain\Entities\User;
use App\Modules\Identity\Domain\ValueObjects\EmailAddress;
use App\Modules\Identity\Domain\ValueObjects\Username;

interface UserRepository
{
    public function existsByEmail(EmailAddress $email): bool;

    public function existsByUsername(Username $username): bool;

    public function findById(int $id): ?User;

    public function findByLogin(string $login): ?User;

    public function save(User $user): User;
}
