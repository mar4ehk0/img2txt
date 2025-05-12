<?php

namespace App\View;

use App\UseCase\TextRecognizer\TextRecognizerResultDto;

class ImageRecogniseUploadView
{
    public function __construct(private TextRecognizerResultDto $dto)
    {
    }

    public function getView(): array
    {
        return [
            'image_id' => $this->dto->imageId,
            'text_id' => $this->dto->textId,
            'text' => $this->dto->text,
        ];
    }
}
