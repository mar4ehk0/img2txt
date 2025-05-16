<?php

namespace App\Controller;

use App\Exception\IAMTokenException;
use App\Exception\ImageProcessingException;
use App\Exception\UploadFileException;
use App\Exception\YandexIAMClientException;
use App\Exception\YandexOCRHttpClientException;
use App\UseCase\TextRecognizer\TextRecognizer;
use App\View\ImageRecogniseUploadView;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageRecogniseController extends BaseController
{
    public function __construct(
        private readonly TextRecognizer $recognizer,
        private readonly LoggerInterface $logger,
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
        } catch (IAMTokenException|YandexIAMClientException $exception) {
            $this->logger->error($exception->getMessage());
            $result = ['error' => true, 'msg' => 'Ошибка при доступе к внешнему сервису. Попробуйте позже.'];
            $code = Response::HTTP_BAD_GATEWAY;
        } catch (YandexOCRHttpClientException|ImageProcessingException $exception) {
            $this->logger->error($exception->getMessage());
            $result = ['error' => true, 'msg' => 'Не удалось распознать изображение. Попробуйте другое.'];
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (UploadFileException $exception) {
            $this->logger->error($exception->getMessage());
            $result = ['error' => true, 'msg' => 'Файл не был загружен. Проверьте его формат и повторите попытку.'];
            $code = Response::HTTP_BAD_REQUEST;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
            $result = ['error' => true, 'msg' => 'Произошла ошибка на сервере. Пожалуйста, попробуйте позже.'];
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new JsonResponse($result, $code);

        return $response;
    }
}
