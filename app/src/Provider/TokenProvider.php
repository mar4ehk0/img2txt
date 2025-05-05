<?php

namespace App\Provider;

class TokenProvider
{
    public function __construct(
        private readonly string $pathToJwtFile
    )
    {
    }

    public function getToken(): string
    {

        $json = json_decode(file_get_contents($this->pathToJwtFile), true);
        $token = $json['token'] ?? null;

        return $token;
    }
}
