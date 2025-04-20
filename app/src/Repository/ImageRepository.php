<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
}
