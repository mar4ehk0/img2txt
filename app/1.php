<?php


// создать команду для symfony create:ocr-jwt поместить в папку Command

require 'vendor/autoload.php';

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

// Чтение данных из файла
$keyData = json_decode(file_get_contents("./config/authorized_key.json"), true);
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
    ->addSignature($jwk, ['alg' => 'PS256', 'typ'=>'JWT', 'kid' => $keyId])
    ->build();


$serializer = new CompactSerializer();
$token = $serializer->serialize($jws, 0);

// Сохранение токена в файл
file_put_contents('jwt_token.txt', $token);
// Вывод токена в консоль
echo "JWT Token: " . $token . PHP_EOL;
