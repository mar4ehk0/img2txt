<?php

namespace App\Controller;

use App\Service\ImageService;
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
         $uploadedFile =    $request->files->get('image');

        var_dump($uploadedFile);
        // /tmp/

//        $result = $this->imageService->upload($dto);

        return new JSONResponse("lorem");
    }

}
