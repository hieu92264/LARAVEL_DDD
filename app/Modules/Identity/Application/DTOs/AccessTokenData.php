<?php

namespace App\Modules\Identity\Application\DTOs;

final readonly class AccessTokenData
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int    $expiresIn
    )
    {

    }

    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'tokenType' => $this->tokenType,
            'expiresIn' => $this->expiresIn
        ];
    }
}
