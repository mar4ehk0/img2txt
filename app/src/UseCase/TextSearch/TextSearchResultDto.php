<?php

namespace App\UseCase\TextSearch;

class TextSearchResultDto
{
    public function __construct(
        public array $value,
    ) {
    }
}
