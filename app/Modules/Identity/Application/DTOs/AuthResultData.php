<?php

namespace App\Modules\Identity\Application\DTOs;

final readonly class AuthResultData
{
    public function __construct(
        public AccessTokenData $token,
        public UserData        $userData
    )
    {

    }

    public function toArray(): array
    {
        return [
            ...$this->token->toArray(),
            'user' => $this->userData->toArray()
        ];
    }
}
