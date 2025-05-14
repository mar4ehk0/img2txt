<?php

namespace App\Repository;

use App\Entity\Text;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TextRepository
{
    private EntityRepository $repo;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repo = $this->entityManager->getRepository(Text::class);
    }

    public function add(Text $image): void
    {
        $this->entityManager->persist($image);
    }

    public function search(string $value)
    {
        // 1 создать запрос через LIKE
        // 2 добавь в бд 3 млн записи TEXT // напиши комманду которая добавляет в Text
        // 3 замерить поиск через LIKE с таблице в 3 млн записей

        return $this->repo->createQueryBuilder('t')
            ->where('LOWER(t.text) LIKE :value')
            ->setParameter('value', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

}
