<?php

namespace App\Command;

use App\Entity\Image;
use App\Entity\Text;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Factory\UlidFactory;

#[AsCommand(name: 'app:generate-partial')]
class GeneratePartialCommand extends Command
{
    private EntityManagerInterface $em;
    private UlidFactory $ulidFactory;

    public function __construct(EntityManagerInterface $em, UlidFactory $ulidFactory)
    {
        parent::__construct();
        $this->em = $em;
        $this->ulidFactory = $ulidFactory;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Генерация данных одним потоком')
            ->addOption('offset', null, InputOption::VALUE_REQUIRED, 'Смещение (offset) начала генерации', 0)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Количество записей для генерации', 100000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        gc_enable();
        ini_set('memory_limit', '6512M');

        $offset = (int) $input->getOption('offset');
        $limit = (int) $input->getOption('limit');
        $faker = Factory::create('ru_RU');
        $now = new \DateTimeImmutable();
        $baseImagePath = '/application/public/file_storage/2025/05/15/01JV9FX3Q7Z0QZBP5H61F22HFT.png';

        $batchSize = 500;

        $output->writeln("Генерация $limit записей с offset = $offset...");

        for ($i = $offset; $i < $offset + $limit; ++$i) {
            $idImage = $this->ulidFactory->create();
            $image = new Image(
                $idImage,
                $idImage->toString(),
                $baseImagePath,
                $now,
                $now
            );

            $idText = $this->ulidFactory->create();
            $text = new Text(
                $idText,
                $faker->realText($faker->numberBetween(50, 500)),
                $image,
                $now,
                $now
            );

            $this->em->persist($image);
            $this->em->persist($text);

            if (($i + 1) % $batchSize === 0) {
                $this->em->flush();
                $this->em->clear();
                gc_collect_cycles();
                $output->writeln("Создано: $i");
            }
        }

        $this->em->flush();
        $this->em->clear();

        $output->writeln("Блок с offset=$offset завершён");

        return Command::SUCCESS;
    }
}
