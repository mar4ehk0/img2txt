<?php

namespace App\Command;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'create:ocr-jwt')]
class GenerateJWT extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $keyData = json_decode(file_get_contents("./config/yandex_ocr/authorized_key.json"), true);
        $privateKeyPem = $keyData['private_key'];
        $keyId = $keyData['id'];
        $serviceAccountId = $keyData['service_account_id'];

// Необходимо удалить заголовок/метаданные из закрытого ключа
        if (strpos($privateKeyPem, "PLEASE DO NOT REMOVE THIS LINE!") === 0) {
            $privateKeyPem = substr($privateKeyPem, strpos($privateKeyPem, "\n") + 1);
        }

        $jwk = JWKFactory::createFromKey(
            $privateKeyPem,
            null,
            [
                'alg' => 'PS256',
                'use' => 'sig',
                'kid' => $keyId,
            ]
        );

        $algorithmManager = new AlgorithmManager([new PS256()]);
        $jwsBuilder = new JWSBuilder($algorithmManager);

        $payload = json_encode([
            'iss' => $serviceAccountId,
            'aud' => "https://iam.api.cloud.yandex.net/iam/v1/tokens",
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
        ]);

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($jwk, ['alg' => 'PS256', 'typ' => 'JWT', 'kid' => $keyId])
            ->build();


        $serializer = new CompactSerializer();
        $token = $serializer->serialize($jws, 0);

// Сохранение токена в файл
        file_put_contents('./config/yandex_ocr/jwt_token.json',
            json_encode(['token' => $token], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


        return Command::SUCCESS;
    }
}


