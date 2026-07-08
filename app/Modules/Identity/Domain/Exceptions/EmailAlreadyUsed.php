<?php

namespace App\Modules\Identity\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class EmailAlreadyUsed extends DomainException
{
    public function __construct()
    {
        parent::__construct(__('identity::exception.email_already_exists'));
    }

    public function errorCode(): string
    {
        return 'EMAIL_ALREADY_USED';
    }
}
