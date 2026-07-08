<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Security;

use App\Core\Application\Contracts\PasswordHasher;
use Illuminate\Support\Facades\Hash;

final class LaravelPasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
    public function verify(string $plainPassword, string $hashedPassword): bool {
        return Hash::check($plainPassword, $plainPassword);
    }
}
