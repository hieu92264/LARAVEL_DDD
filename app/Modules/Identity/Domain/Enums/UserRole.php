<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
}
