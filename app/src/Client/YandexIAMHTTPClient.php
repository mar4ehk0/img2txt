<?php

namespace App\Client;

use App\Provider\IAMTokenFileProvider;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class YandexIAMHTTPClient
{
    public function __construct(
        private HttpClientInterface $client,
        private readonly string $urlYandexIAM
    ) {

    }

    public function request(string $jwt): string
    {
        $response = $this->client->request(
            'POST',
            $this->urlYandexIAM,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    "jwt" => $jwt,
                ], JSON_THROW_ON_ERROR),
            ]);

        $content = $response->getContent();
        $content = json_decode($content, true);

        if (empty($content['iamToken'])) {
            // throw exception
        }

        return $content['iamToken'];
    }
}
