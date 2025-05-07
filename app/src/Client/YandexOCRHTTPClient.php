<?php

namespace App\Client;

use App\Interface\TokenFileProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


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


        $texts = [];
        foreach ($content['result']['text_annotation']['blocks'] as $block) {
            foreach ($block['lines'] as $line) {
                if (!empty($line['alternatives'][0]['text'])) {
                    $texts[] = $line['alternatives'][0]['text'];
                }
            }
        }
        $resultText = implode(' ', $texts);


        return !empty($resultText) ? $resultText : null;
    }
}
