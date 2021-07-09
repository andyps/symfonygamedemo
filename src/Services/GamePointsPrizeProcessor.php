<?php


namespace App\Services;

use App\Entity\Game;

class GamePointsPrizeProcessor extends GamePrizeProcessor
{
    protected function doProcessGamePrize(Game $game): bool
    {
        $points = $game->getIsConvertedToPoints() ? $game->getPoints() : $game->getAmount();
        $game->getPlayer()->addPoints($points);
        $game->setStatus(Game::STATUS_PROCESSED);
        return true;
    }
}
