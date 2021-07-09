<?php


namespace App\Services;

use App\Entity\Game;
use App\Entity\Prize;

class GamePrizeProcessorFactory
{
    public function __construct(private BankApiService $bankApiService)
    {
    }

    /**
     * @param Game $game
     * @return GamePrizeProcessor
     * @throws \Exception If a game doesn't have a prize or the prize type is unknown.
     */
    public function createPrizeProcessor(Game $game): GamePrizeProcessor
    {
        $prize = $game->getPrize();
        if (is_null($prize)) {
            throw new \Exception('Cannot process game without a prize');
        }

        if ($prize->getType() === Prize::TYPE_POINTS || $game->getIsConvertedToPoints()) {
            return new GamePointsPrizeProcessor();
        } elseif ($prize->getType() === Prize::TYPE_PHYSICAL) {
            return new GamePhysicalPrizeProcessor();
        } elseif ($prize->getType() === Prize::TYPE_MONEY) {
            return new GameMoneyPrizeProcessor($this->bankApiService);
        }

        throw new \Exception('Unknown prize type. Cannot find an appropriate processor.');
    }
}
