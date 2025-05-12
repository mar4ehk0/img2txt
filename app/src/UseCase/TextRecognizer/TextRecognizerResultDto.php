<?php

namespace App\UseCase\TextRecognizer;

use Symfony\Component\Uid\Ulid;

readonly class TextRecognizerResultDto
{
    public function __construct(
        public Ulid $imageId,
        public Ulid $textId,
        public string $text
    ) {
    }
}
