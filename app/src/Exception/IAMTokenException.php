<?php

namespace App\Exception;

class IAMTokenException extends \Exception
{
    public static function tokenNotFound(): self
    {
        return new self('IAM token not found in the response from Yandex IAM.');
    }
}
