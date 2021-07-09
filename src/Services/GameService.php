<?php


namespace App\Services;

use App\Entity\Game;
use App\Entity\Prize;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GamePrizeProcessorFactory $prizeProcessorFactory
    ) {
    }

    public function createGame(User $user): Game
    {
        $prize = $this->choosePrize();

        $game = new Game();
        $game->setPlayer($user);
        if ($prize) {
            $game->setStatus(Game::STATUS_WIN);
            $game->setPrize($prize);
            $game->setAmount($this->choosePrizeAmount($prize));
        } else {
            $game->setStatus(Game::STATUS_LOST);
            $game->setAmount(0);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }

    public function approve(Game $game)
    {
        $game->approve();
        $this->entityManager->flush();
    }

    public function reject(Game $game)
    {
        $game->reject();
        $this->entityManager->flush();
    }

    public function convert(Game $game)
    {
        $game->approve();
        if ($game->getStatus() !== Game::STATUS_APPROVED) {
            return;
        }

        $prize = $game->getPrize();
        if ($prize->getIsConvertable()) {
            $conversionRate = $prize->getConvertionRate();
            $points = $conversionRate * $game->getAmount();
            $game->setIsConvertedToPoints(true);
            $game->setConvertionRate($conversionRate);
            $game->setPoints($points);
        }

        $this->entityManager->flush();
    }

    public function complete(Game $game)
    {
        $prizeProcessor = $this->prizeProcessorFactory->createPrizeProcessor($game);
        $prizeProcessor->processGamePrize($game);

        $this->entityManager->flush();
    }

    private function choosePrize(): ?Prize
    {
        $prizeRepo = $this->entityManager->getRepository(Prize::class);
        $prizes = $prizeRepo->findAll();
        $randIdx = random_int(-1, count($prizes) - 1);
        return $randIdx >= 0 ? $prizes[$randIdx] : null;
    }

    private function choosePrizeAmount(Prize $prize): int
    {
        $from = min(1, $prize->getAmountFrom());
        $to = max($from, $prize->getAmountTo());
        return random_int($from, $to);
    }
}
