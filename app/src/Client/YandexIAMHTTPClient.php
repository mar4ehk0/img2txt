<?php

namespace App\Client;

use App\Exception\IAMTokenException;
use App\Exception\YandexIAMClientException;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
        try{
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
                ]
            );

            $content = $response->getContent();
            $content = json_decode($content, true);

            if (empty($content['iamToken'])) {
                throw IamTokenException::tokenNotFound();
            }
        }catch (TransportExceptionInterface |
        ClientExceptionInterface |
        ServerExceptionInterface |
        RedirectionExceptionInterface |
        JsonException |
        \Throwable $e){
            throw YandexIAMClientException::requestFailed($e->getMessage());
        }
        return $content['iamToken'];
    }
}
