<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class InvalidTokenException extends UnauthorizedHttpException
{
    public function __construct(string $message = 'Invalid or missing JWT token')
    {
        parent::__construct('Bearer', $message);
    }
}
