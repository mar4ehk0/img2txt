<?php

namespace App\Provider;

class IAMTokenFileProvider
{
    public function __construct(
        private string $pathToIAMFile
    ) {
    }

    public function getToken(): string // сделай интерфейс для данного метода
    {

        $json = json_decode(file_get_contents($this->pathToIAMFile), true);
        $token = $json['token'] ?? null;

        return $token;
    }
}
