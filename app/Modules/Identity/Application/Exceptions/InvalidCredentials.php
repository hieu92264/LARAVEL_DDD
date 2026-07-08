<?php

namespace App\Modules\Identity\Application\Exceptions;

use RuntimeException;

final class InvalidCredentials extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(__('identity::auth.invalid_credentials'));
    }

    public function errorCode(): string
    {
        return 'INVALID_CREDENTIALS';
    }
}
