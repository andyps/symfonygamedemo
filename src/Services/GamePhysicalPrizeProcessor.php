<?php


namespace App\Services;

use App\Entity\Game;

class GamePhysicalPrizeProcessor extends GamePrizeProcessor
{
    protected function doProcessGamePrize(Game $game): bool
    {
        $game->setStatus(Game::STATUS_PROCESSED);
        return true;
    }
}
