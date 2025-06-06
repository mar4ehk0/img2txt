<?php

namespace App\Client;

use App\Exception\IAMTokenException;
use App\Exception\YandexIAMClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YandexIAMHTTPClient
{
    public function __construct(
        private HttpClientInterface $client,
        private readonly string $urlYandexIAM,
    ) {
    }

    public function request(string $jwt): string
    {
        try {
            $response = $this->client->request(
                'POST',
                $this->urlYandexIAM,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'jwt' => $jwt,
                    ], JSON_THROW_ON_ERROR),
                ]
            );

            $content = $response->getContent();
            $content = json_decode($content, true);

            if (empty($content['iamToken'])) {
                throw IAMTokenException::tokenNotFound();
            }
        } catch (\Throwable $e) {
            throw YandexIAMClientException::requestFailed($e->getMessage());
        }

        return $content['iamToken'];
    }
}
