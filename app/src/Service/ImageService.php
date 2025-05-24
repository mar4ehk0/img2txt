<?php

namespace App\Service;

use App\Entity\Image;
use App\Exception\UploadFileException;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Factory\UlidFactory;

class ImageService
{
    public function __construct(
        private readonly string $fileStorage,
        private UlidFactory $ulidFactory,
        private ImageRepository $imageRepository,
    ) {
    }

    /**
     * @throws UploadFileException
     */
    public function upload(UploadedFile $file): Image
    {
        $extension = $file->getClientOriginalExtension();
        $id = $this->ulidFactory->create();
        $newFilePath = $this->createFilePath($id->toString(), $extension);

        if (!move_uploaded_file($file->getPathname(), $newFilePath)) {
            throw UploadFileException::createMoveUpload($file->getPathname(), $newFilePath);
        }

        $now = new \DateTimeImmutable();

        $entity = new Image(
            $id,
            $id->toString(),
            $newFilePath,
            $now,
            $now
        );
        $this->imageRepository->add($entity);

        return $entity;
    }

    /**
     * @throws UploadFileException
     */
    private function createFilePath(string $newFileName, string $extension): string
    {
        $datePath = date('Y/m/d');
        $fullPath = rtrim($this->fileStorage, '/') . '/' . $datePath;

        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0777, true) && !is_dir($fullPath)) {
                throw UploadFileException::createMkDir($fullPath);
            }
        }

        return sprintf('%s/%s.%s', $fullPath, $newFileName, $extension);
    }
}
