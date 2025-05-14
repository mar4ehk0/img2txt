<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TextSearchController extends BaseController
{
    #[Route('/text-search', name: 'text_search', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([]);
    }
}
