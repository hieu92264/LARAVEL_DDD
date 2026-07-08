<?php

namespace App\Modules\Identity\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;

final class AccountInactive extends DomainException
{
    public function __construct()
    {
        parent::__construct(__('identity::exception.account_inactive'));
    }

    public function errorCode(): string
    {
        // TODO: Implement errorCode() method.
        return 'ACCOUNT_INACTIVE';
    }
}
