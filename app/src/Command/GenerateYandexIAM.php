<?php

namespace App\Command;

use App\Client\YandexIAMHTTPClient;
use App\Exception\YandexIAMClientException;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;


#[AsCommand(name: 'ocr:generate-iam')]
class GenerateYandexIAM extends Command
{
    public function __construct(
        protected YandexIAMHTTPClient $client,
        private string $pathToYandexAuthorizedKey,
        private string $pathToIAMFile,
        private string $urlYandexIAM,
        private LoggerInterface $logger,
    ) {
        $name = 'ocr:generate-iam';
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jwt = $this->createJWT();

        try {
            $iamToken = $this->client->request($jwt);
        } catch (YandexIAMClientException $e) {
            $this->logger->error('Error when requesting an IAM token: ' . $e->getMessage());
            $output->writeln('<error>Failed to get the IAM token</error>');
            return Command::FAILURE;
        }

        $result = file_put_contents(
            $this->pathToIAMFile,
            json_encode(['IAMToken' => $iamToken], JSON_PRETTY_PRINT)
        );
        if ($result === false) {
            $this->logger->error('Failed to save the IAM token to a file:' . $this->pathToIAMFile);
            throw YandexIAMClientException::failedToWriteIAMFile($this->pathToIAMFile);
        }


        $output->writeln('Generated Token');
        return Command::SUCCESS;
    }

    private function createJWT(): string
    {
        $keyData = json_decode(file_get_contents($this->pathToYandexAuthorizedKey), true);
        $privateKeyPem = $keyData['private_key'];
        $keyId = $keyData['id'];
        $serviceAccountId = $keyData['service_account_id'];

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
            'aud' => $this->urlYandexIAM,
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

        return $serializer->serialize($jws, 0);
    }
}


