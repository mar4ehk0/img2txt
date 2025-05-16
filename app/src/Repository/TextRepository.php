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

    public function add(Text $text): void
    {
        $this->entityManager->persist($text);
    }

    public function search(string $value): array
    {
        // поиск по "вошел" 10.7332 с
        // поиск по "Как не быть" 11.5323 С
        // поиск по "После обеда" 11.9630 С

        //        $start = microtime(true);

        $result = $this->repo->createQueryBuilder('t')
            ->where('LOWER(t.text) LIKE :value')
            ->setParameter('value', '%'.mb_strtolower(trim($value)).'%')
            ->getQuery()
            ->getResult();
        $end = microtime(true);

        //        $duration = $end - $start;
        //        echo sprintf("Время выполнения поиска: %.4f секунд\n", $duration);

        return $result;
    }
}
