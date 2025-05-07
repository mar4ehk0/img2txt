<?php

namespace App\Provider;

use App\Exception\YandexIAMClientException;
use App\Interface\TokenFileProviderInterface;

class IAMTokenFileProvider implements TokenFileProviderInterface
{
    public function __construct(
        private string $pathToIAMFile
    ) {
    }

    public function getToken(): string
    {
        if (!file_exists($this->pathToIAMFile)) {
            throw YandexIAMClientException::iamFileNotFound($this->pathToIAMFile);
        }

        $json = json_decode(file_get_contents($this->pathToIAMFile), true);

        if (!isset($json['IAMToken']) || !is_string($json['IAMToken'])) {
            throw YandexIAMClientException::tokenNotFoundInFile($this->pathToIAMFile);
        }

        $IAMToken = $json['IAMToken'];

        return $IAMToken;
    }
}
