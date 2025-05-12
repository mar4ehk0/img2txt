<?php

namespace App\Controller;

use App\Exception\IAMTokenException;
use App\UseCase\TextRecognizer\TextRecognizer;
use App\View\ImageRecogniseUploadView;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ImageRecogniseController extends BaseController
{

    public function __construct(
        private readonly TextRecognizer $recognizer,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/image-recognise', name: 'image_recognise', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');
        try {
            $dto = $this->recognizer->handle($uploadedFile);

            $view = new ImageRecogniseUploadView($dto);

            $result = $view->getView();

            $code = Response::HTTP_OK;
        } catch (IAMTokenException $exception) {
            $result = ['error' => true, 'msg' => 'написать свой текст'];
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());

            $result = ['error' => true, 'msg' => 'написать свой текст'];
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new JsonResponse($result, $code);

        return $response;
    }
}
