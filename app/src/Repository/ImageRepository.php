<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageRepository
{
    private EntityRepository $repo;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repo = $this->entityManager->getRepository(Image::class);
    }

    public function add(Image $image): void
    {
        $this->entityManager->persist($image);
    }

    public function findById(string $id): Image
    {
        $image = $this->repo->find($id);

        if (!$image) {
            throw new NotFoundHttpException(sprintf('Image with ID "%s" not found', $id));
        }

        return $image;
    }
}
