<?php

namespace App\Exception;

final class YandexOCRHttpClientException extends \Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingKey(string $path): self
    {
        return new self("Missing required key: '$path' in OCR response.");
    }

    public static function notArray(string $path): self
    {
        return new self("Expected an array at: '$path' in OCR response.");
    }

    public static function missingLines(int $blockIndex): self
    {
        return new self(sprintf("Block %d is missing 'lines' or it's not an array.", $blockIndex));
    }

    public static function missingAlternativeText(int $blockIndex, int $lineIndex): self
    {
        return new self(
            sprintf(
                "Line %d in block %d is missing 'alternatives[0][\"text\"]' or it's empty.",
                $lineIndex,
                $blockIndex
            )
        );
    }
}
