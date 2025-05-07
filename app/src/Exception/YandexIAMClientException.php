<?php

namespace App\Exception;

use Exception;

final class YandexIAMClientException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function requestFailed(string $reason): self
    {
        return new self(sprintf('IAM request failed: %s', $reason));
    }

    public static function failedToWriteIAMFile(string $filePath): self
    {
        return new self(sprintf('Failed to write IAM token to file: %s', $filePath));
    }

    public static function iamFileNotFound(string $filePath): self
    {
        return new self(sprintf('IAM token file not found: %s', $filePath));
    }

    public static function tokenNotFoundInFile(string $filePath): self
    {
        return new self(sprintf('IAM token not found in file: %s', $filePath));
    }
}