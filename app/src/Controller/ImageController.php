<?php

namespace App\Controller;

use App\UseCase\TextRecognizer\TextRecognizerHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends BaseController
{
    public function __construct(
        private readonly TextRecognizerHandler $recognizer,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/images', name: 'images_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $msg = 'File uploaded';

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');
        try {
            $this->recognizer->handle($uploadedFile);
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());

            $this->logger->error($exception->getMessage());
            $msg = 'Something went wrong';
        }

        return new JsonResponse($msg);
    }
}
