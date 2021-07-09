<?php


namespace App\Services;

use App\Entity\Game;

abstract class GamePrizeProcessor
{
    /**
     * @param Game $game
     * @return bool
     * @throws \Exception If game prize cannot be processed.
     */
    public function processGamePrize(Game $game): bool
    {
        if ($game->getStatus() !== Game::STATUS_APPROVED) {
            throw new \Exception('Wrong status to process prize. Should be "' . Game::STATUS_APPROVED .
                '" but it is "' . $game->getStatus() . '"');
        }

        return $this->doProcessGamePrize($game);
    }
    abstract protected function doProcessGamePrize(Game $game): bool;
}
