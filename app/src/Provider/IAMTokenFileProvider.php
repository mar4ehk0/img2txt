<?php

namespace App\Provider;

use App\Interface\TokenFileProviderInterface;

class IAMTokenFileProvider implements TokenFileProviderInterface
{
    public function __construct(
        private string $pathToIAMFile
    ) {
    }

    public function getToken(): string
    {

        $json = json_decode(file_get_contents($this->pathToIAMFile), true);
        $iamToken = $json['iamToken'] ?? null;

        return $iamToken;
    }
}
