<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:generate-parallel')]
class GenerateParallelCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Генерация данных в несколько потоков')
            ->addOption('total', null, InputOption::VALUE_OPTIONAL, 'Сколько всего записей генерировать', 3000000)
            ->addOption('chunk', null, InputOption::VALUE_OPTIONAL, 'Размер одного чанка', 50000)
            ->addOption('parallel', null, InputOption::VALUE_OPTIONAL, 'Количество параллельных процессов', 1)
            ->addOption('console-bin', null, InputOption::VALUE_OPTIONAL, 'Путь до bin/console', './bin/console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $total = (int) $input->getOption('total');
        $chunk = (int) $input->getOption('chunk');
        $maxParallel = (int) $input->getOption('parallel');
        $binConsole = $input->getOption('console-bin');

        $running = [];

        $waitForSlot = function () use (&$running, $maxParallel): void {
            while (count($running) >= $maxParallel) {
                foreach ($running as $pid => $process) {
                    $status = proc_get_status($process['resource']);
                    if (!$status['running']) {
                        proc_close($process['resource']);
                        unset($running[$pid]);
                    }
                }
                usleep(100_000);
            }
        };

        for ($offset = 0; $offset < $total; $offset += $chunk) {
            $waitForSlot();

            $cmd = sprintf(
                'php %s app:generate-partial --offset=%d --limit=%d',
                escapeshellarg($binConsole),
                $offset,
                $chunk
            );

            $output->writeln(" Запуск процесса: $cmd");

            $descriptorspec = [
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ];

            $process = proc_open($cmd, $descriptorspec, $pipes);

            if (is_resource($process)) {
                $running[$offset] = [
                    'resource' => $process,
                    'pipes' => $pipes,
                ];
            } else {
                $output->writeln("<error>❌ Не удалось запустить процесс offset=$offset</error>");

                return Command::FAILURE;
            }
        }

        // Дождаться всех
        while (count($running) > 0) {
            foreach ($running as $pid => $process) {
                $status = proc_get_status($process['resource']);
                if (!$status['running']) {
                    $out = stream_get_contents($process['pipes'][1]);
                    $err = stream_get_contents($process['pipes'][2]);

                    fclose($process['pipes'][1]);
                    fclose($process['pipes'][2]);

                    proc_close($process['resource']);
                    unset($running[$pid]);

                    $output->writeln(" Процесс offset=$pid завершён");
                    if ($out) {
                        $output->writeln("<info>$out</info>");
                    }
                    if ($err) {
                        $output->writeln("<error>$err</error>");
                    }
                }
            }
            usleep(100_000);
        }

        $output->writeln('<info> Все процессы завершены</info>');

        return Command::SUCCESS;
    }
}
