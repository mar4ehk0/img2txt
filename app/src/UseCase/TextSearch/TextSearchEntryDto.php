<?php

namespace App\UseCase\TextSearch;

readonly class TextSearchEntryDto
{
    public function __construct(
        public string $value,
    ) {
    }
}
