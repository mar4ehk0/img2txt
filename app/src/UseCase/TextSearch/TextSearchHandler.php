<?php

namespace App\UseCase\TextSearch;

use App\Repository\TextRepository;
use Doctrine\Common\Collections\ArrayCollection;

class TextSearchHandler
{
    public function __construct(
        private TextRepository $textRepository
    ) {
    }

    public function handle(TextSearchEntryDto $dto): TextResultDto
    {
        $texts = new ArrayCollection($this->textRepository->search($dto->value)); // должен вернуть коллекцию Text

        $dto = new TextResultDto($texts);

        return $dto;
    }
}
