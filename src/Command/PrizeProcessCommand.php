<?php

namespace App\Command;

use App\Repository\GameRepository;
use App\Services\GameService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prize:process',
    description: 'Process all wins and prizes',
)]
class PrizeProcessCommand extends Command
{
    public const BATCH_SIZE = 5;

    public function __construct(private GameService $gameService, private GameRepository $gameRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $processedCount = 0;
        $errors = [];
        $gamesToComplete = $this->gameRepository->findGamesToComplete(self::BATCH_SIZE);
        foreach ($gamesToComplete as $game) {
            try {
                $this->gameService->complete($game);
                $processedCount++;
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
                $errors[] = $e->getMessage();
            }
        }

        $errorsCount = count($errors);
        $io->success(sprintf('Processed "%d" prizes.', $processedCount));
        $exitStatus = Command::SUCCESS;
        if ($errorsCount > 0) {
            $exitStatus = Command::FAILURE;
            $io->warning(sprintf('Number of not processed prizes: "%d".', $errorsCount));
            $io->error($errors);
        }

        return $exitStatus;
    }
}
