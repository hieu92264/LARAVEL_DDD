<?php

namespace App\Modules\Identity\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class UsernameAlreadyUsed extends DomainException
{
    public function __construct(string $username)
    {
        parent::__construct(__('identity::exception.username_already_exists', ['username' => $username]));
    }

    public function errorCode(): string
    {
        return 'USERNAME_ALREADY_USED';
    }
}
