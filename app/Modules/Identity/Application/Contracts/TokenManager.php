<?php

namespace App\Modules\Identity\Application\Contracts;

use App\Modules\Identity\Application\DTOs\AccessTokenData;

interface TokenManager
{
    public function issueForUserId(int $userId): AccessTokenData;

    public function refresh(): AccessTokenData;

    public function invalidate(): void;

    public function currentUserId(): ?int;
}
