<?php

namespace App\Service;

use App\Client\YandexOCRHTTPClient;
use App\Entity\Image;
use App\Entity\Text;
use App\Exception\ImageProcessingException;
use App\Repository\TextRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Factory\UlidFactory;

class OCRService
{
    public function __construct(
        private UlidFactory $ulidFactory,
        private YandexOCRHTTPClient $httpClient,
        private TextRepository $repository,
        private LoggerInterface $logger,
    ) {
    }

    public function recognize(Image $image): Text
    {
        try {
            $content = $this->httpClient->request($image->getPath());
        } catch (\Throwable $exception) {
            $this->logger->error('Error while executing OCR request:' . $exception->getMessage(), [
                'image_path' => $image->getPath(),
                'exception' => $exception,
            ]);
            throw new ImageProcessingException('Failed to process the image: ' . $image->getPath(), previous: $exception);
        }

        $id = $this->ulidFactory->create();
        $now = new \DateTimeImmutable();

        $text = new Text($id, $content, $image, $now, $now);
        $this->repository->add($text);

        return $text;
    }
}
