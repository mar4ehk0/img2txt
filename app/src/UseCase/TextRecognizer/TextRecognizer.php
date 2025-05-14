<?php

namespace App\UseCase\TextRecognizer;

use App\Exception\UploadFileException;
use App\Service\ImageService;
use App\Service\OCRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TextRecognizer
{
    public function __construct(
        private ImageService $imageService,
        private OCRService $ocrService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(UploadedFile $file): TextRecognizerResultDto
    {
        try {
            $image = $this->imageService->upload($file);

            $text = $this->ocrService->recognize($image);
        } catch (UploadFileException $e) {
            // залогировать ошибку и выкинуть исключение
        }

        $dto = new TextRecognizerResultDto(
            $image->getId(),
            $text->getId(),
            $text->getText(),
        );

        $this->entityManager->flush();

        return $dto;
    }
}
