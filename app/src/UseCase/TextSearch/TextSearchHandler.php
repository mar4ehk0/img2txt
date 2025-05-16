<?php

namespace App\UseCase\TextSearch;

use App\Repository\TextRepository;

class TextSearchHandler
{
    public function __construct(
        private TextRepository $textRepository,
    ) {
    }

    public function handle(TextSearchEntryDto $dto): TextSearchResultDto
    {
        $texts = $this->textRepository->search($dto->value);

        $dto = new TextSearchResultDto($texts);

        return $dto;
    }
}
