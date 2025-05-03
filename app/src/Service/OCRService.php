<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Text;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OCRService
{
    public function __construct(
        private UlidFactory $ulidFactory,
        private HttpClientInterface $client,
    )
    {
    }

    public function recognize(Image $image): Text
    {
        try {
//            HTTPYandexOCRClient(url) сделать вызов этого клиента тут с переменной $image->getPath()
            $response = $this->client->request(
                'POST',
                'https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    ],
                    'body' => json_encode([
                        "mimeType" => "JPEG",
                        "languageCodes" => ["*"],
                        "model" => "page",
                        "content" => base64_encode(file_get_contents($image->getPath())),
                    ]),
                ]
            );
        } catch (Exception $exception) {
//            var_dump($exception->getMessage());
//            die();
        }


        $content = $response->getContent();
        $content = json_decode($content);

        // клиент должен отдавать либо null либо string, в клиенте обработай каждую строку trim
        $content = $content?->result?->textAnnotation?->fullText;
        $id = $this->ulidFactory->create();
        $now = new DateTimeImmutable();

        $text = new Text($id, $content, $image, $now, $now);

        return $text;
    }


}
