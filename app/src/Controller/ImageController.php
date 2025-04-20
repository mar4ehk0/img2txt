<?php

namespace App\Controller;

use App\Service\ImageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends BaseController
{

    public function __construct(private readonly ImageService $imageService)
    {
    }

    #[Route('/images', name: 'images_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');
        $this->imageService->upload($uploadedFile);

        return new JSONResponse("lorem");
    }

}
