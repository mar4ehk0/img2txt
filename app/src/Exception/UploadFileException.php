<?php

namespace App\Exception;

use Exception;

final class UploadFileException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function createMoveUpload(string $filePath, string $newFilePath): UploadFileException
    {
        $msg = 'Can not move file: %s to new path %s';
        return new self(sprintf($msg, $filePath, $newFilePath));
    }

    public static function createMkDir(string $fullPath): UploadFileException
    {
        return new self(sprintf('Directory "%s" was not created', $fullPath));
    }
}
