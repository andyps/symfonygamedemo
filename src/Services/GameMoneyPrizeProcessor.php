<?php


namespace App\Services;

use App\Entity\Game;

class GameMoneyPrizeProcessor extends GamePrizeProcessor
{
    public function __construct(private BankApiService $bankApi)
    {
    }

    protected function doProcessGamePrize(Game $game): bool
    {
        $this->bankApi->sendMoneyToUser($game->getPlayer(), $game->getAmount());

        $game->setStatus(Game::STATUS_PROCESSED);
        return true;
    }
}
