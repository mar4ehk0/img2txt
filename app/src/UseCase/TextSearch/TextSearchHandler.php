<?php

namespace App\UseCase\TextSearch;

use App\Repository\TextRepository;

class TextSearchHandler
{
    public function __construct(
        private TextRepository $textRepository
    ) {
    }

    public function handle(TextSearchEntryDto $dto): void
    {
        $this->textRepository->search($dto->value); // должен вернуть коллекцию Text

        $dto->value;

        // должен создать ResultDto которая содержит коллекцию
    }
}
