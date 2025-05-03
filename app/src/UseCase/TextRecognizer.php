<?php

namespace App\UseCase;

use App\Exception\UploadFileException;
use App\Service\ImageService;
use App\Service\OCRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TextRecognizer
{
    public function __construct(
        private ImageService  $imageService,
        private OCRService $ocrService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(UploadedFile $file): void
    {
        try {
            $img = $this->imageService->upload($file);

            $this->ocrService->recognize($img);
        } catch (UploadFileException $e) {

        }

//        $this->entityManager->flush();
    }
}
