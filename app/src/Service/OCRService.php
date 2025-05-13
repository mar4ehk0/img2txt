<?php

namespace App\Service;

use App\Client\YandexOCRHTTPClient;
use App\Entity\Image;
use App\Entity\Text;
use Symfony\Component\Uid\Factory\UlidFactory;

class OCRService
{
    public function __construct(
        private UlidFactory $ulidFactory,
        private YandexOCRHTTPClient $httpClient,
    ) {
    }

    public function recognize(Image $image): Text
    {
        try {
            $content = $this->httpClient->request($image->getPath());
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        $id = $this->ulidFactory->create();
        $now = new \DateTimeImmutable();

        $text = new Text($id, $content, $image, $now, $now);

        return $text;
    }
}
