<?php

namespace App\Client;

use App\Exception\YandexIAMClientException;
use App\Exception\YandexOCRHttpClientException;
use App\Interface\TokenFileProviderInterface;
use JsonException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;


class YandexOCRHTTPClient
{
    public function __construct(
        private HttpClientInterface $client,
        private TokenFileProviderInterface $IAMTokenProvider,
        private string $urlYandexOCR
    )
    {
    }


    public function request(string $imagePath): ?string
    {
        try {
            $IAMToken = $this->IAMTokenProvider->getToken();
            $response = $this->client->request(
                'POST',
                $this->urlYandexOCR,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$IAMToken,
                    ],
                    'body' => json_encode([
                        "mimeType" => "JPEG",
                        "languageCodes" => ["*"],
                        "model" => "page",
                        "content" => base64_encode(file_get_contents($imagePath)),
                    ]),
                ]
            );

            $content = $response->getContent();
            $content = json_decode($content, true);

            $result = $this->contentProcessing($content);
        } catch (Throwable $e) {
            throw $e;
        }

        return $result;
    }

    private function contentProcessing($content)
    {
        $texts = [];

        // TODO - переписать вынести в метод validateContent,
        // сделать проверку каждого элемента isset($content['result'])
        if (
            !isset($content['result']['textAnnotation']['blocks']) ||
            !is_array($content['result']['textAnnotation']['blocks'])
        ) {
            throw YandexOCRHttpClientException::missingBlocks();
        }

        foreach ($content['result']['textAnnotation']['blocks'] as $blockIndex => $block) {
            if (!isset($block['lines']) || !is_array($block['lines'])) {
                throw YandexOCRHttpClientException::missingLines($blockIndex);
            }

            foreach ($block['lines'] as $lineIndex => $line) {
                if (
                    !isset($line['text']) ||
                    !is_string($line['text']) ||
                    empty(trim($line['text']))
                ) {
                    throw YandexOCRHttpClientException::missingAlternativeText($blockIndex, $lineIndex);
                }

                $texts[] = trim($line['text']);
            }
        }



        $resultText = implode(' ', $texts);

        return $resultText;
    }
}

