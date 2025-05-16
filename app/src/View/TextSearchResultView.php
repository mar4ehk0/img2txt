<?php

namespace App\View;

use App\Entity\Text;
use App\UseCase\TextSearch\TextSearchResultDto;

class TextSearchResultView
{
    public function __construct(private TextSearchResultDto $dto)
    {
    }

    public function getView(): array
    {
        return array_map(
            fn (Text $text) => $this->formatText($text),
            $this->dto->value
        );
    }

    private function formatText(Text $text): array
    {
        return [
            'id' => $text->getId()->toBase32(),
            'text' => $text->getText(),
        ];
    }
}
