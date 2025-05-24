<?php

namespace App\UseCase\TextSearch;

use App\Entity\Text;

readonly class TextSearchResultDto
{
    public function __construct(
        /**
         * @param Text[] $value
         */
        public array $value,
    ) {
    }
}
