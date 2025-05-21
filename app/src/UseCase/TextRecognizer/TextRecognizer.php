<?php

namespace App\UseCase\TextRecognizer;

use App\Exception\UploadFileException;
use App\Service\ImageService;
use App\Service\OCRService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TextRecognizer
{
    public function __construct(
        private ImageService $imageService,
        private OCRService $ocrService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(UploadedFile $file): TextRecognizerResultDto
    {
        try {
            $image = $this->imageService->upload($file);

            $text = $this->ocrService->recognize($image);
        } catch (UploadFileException $e) {
            $this->logger->error('Error when uploading a file: '.$e->getMessage(), [
                'exception' => $e,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
            throw $e;
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
