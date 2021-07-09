<?php

namespace App\Tests\Services;

use App\Entity\Game;
use App\Entity\Prize;
use App\Services\GamePrizeProcessorFactory;
use App\Services\GameService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    public function testConvertMoney(): void
    {
        // given
        $gameService = $this->createGameService();

        $game = new Game();
        $game->setIsConvertedToPoints(false);
        $game->setStatus(Game::STATUS_WIN);
        $game->setAmount(2);

        $prize = new Prize();
        $prize->setType(Prize::TYPE_MONEY);
        $prize->setIsConvertable(true);
        $prize->setConvertionRate(7);
        $game->setPrize($prize);

        // when
        $gameService->convert($game);

        // then
        $this->assertTrue($game->getIsConvertedToPoints());
        $this->assertEquals($game->getConvertionRate(), 7);
        $this->assertEquals($game->getPoints(), 14);
    }

    public function testConvertPhysicalItem(): void
    {
        // given
        $gameService = $this->createGameService();

        $game = new Game();
        $game->setIsConvertedToPoints(false);
        $game->setStatus(Game::STATUS_WIN);
        $game->setAmount(2);

        $prize = new Prize();
        $prize->setType(Prize::TYPE_PHYSICAL);
        $prize->setIsConvertable(false);
        $game->setPrize($prize);

        // when
        $gameService->convert($game);

        // then
        $this->assertFalse($game->getIsConvertedToPoints());
        $this->assertNotEquals($game->getConvertionRate(), 7);
        $this->assertNotEquals($game->getPoints(), 14);
    }

    public function createGameService(): GameService
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $processFactory = $this->createMock(GamePrizeProcessorFactory::class);
        return new GameService($entityManager, $processFactory);
    }
}
