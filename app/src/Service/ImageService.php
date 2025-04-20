<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Factory\UlidFactory;

class ImageService
{
    public function __construct(
        private readonly string $fileStorage,
        private UlidFactory $ulidFactory,
        private ImageRepository $imageRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function upload(UploadedFile $file): void
    {
        $extension = $file->getClientOriginalExtension();
        $id = $this->ulidFactory->create();
        $newFilePath = $this->createFilePath($id->toString(), $extension);

        //
        if (!move_uploaded_file($file->getPathname(), $newFilePath)) {
            throw new \RuntimeException();
        }

        $now = new DateTimeImmutable();

        $entity = new Image(
            $id,
            $id->toString(),
            $newFilePath,
            $now,
            $now
        );

        $this->imageRepository->add($entity);
        $this->entityManager->flush();
    }

    private function createFilePath(string $newFileName, string $extension): string
    {
        // 2025/04/16
        return sprintf('%s/%s.%s', $this->fileStorage, $newFileName, $extension);
    }

}
