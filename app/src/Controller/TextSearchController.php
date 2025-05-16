<?php

namespace App\Controller;

use App\UseCase\TextSearch\TextSearchEntryDto;
use App\UseCase\TextSearch\TextSearchHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TextSearchController extends BaseController
{
    public function __construct(
        private readonly TextSearchHandler $search,
    ) {
    }

    #[Route('/text-search', name: 'text_search', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new TextSearchEntryDto($data['text']);
        $result = $this->search->handle($dto);
        dd($result);

        return new JsonResponse($result);
    }
}
