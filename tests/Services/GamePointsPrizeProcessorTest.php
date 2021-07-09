<?php

namespace App\Tests\Services;

use App\Entity\Game;
use App\Entity\Prize;
use App\Entity\User;
use App\Services\GamePointsPrizeProcessor;
use PHPUnit\Framework\TestCase;

class GamePointsPrizeProcessorTest extends TestCase
{
    private GamePointsPrizeProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new GamePointsPrizeProcessor();
    }

    private function createGame($status, $prizeType): Game
    {
        $game = new Game();
        $game->setStatus($status);
        $game->setIsConvertedToPoints(false);
        $game->setAmount(2);

        $prize = new Prize();
        $prize->setIsConvertable(false);
        $prize->setType($prizeType);
        $game->setPrize($prize);

        $user = new User();
        $user->setPoints(5);
        $game->setPlayer($user);
        return $game;
    }

    public function testConvertedMoneyPrizeIsProcessed(): void
    {
        // given
        $game = $this->createGame(Game::STATUS_APPROVED, Prize::TYPE_MONEY);
        $game->setIsConvertedToPoints(true);
        $game->setConvertionRate(5);
        $game->setPoints(10);

        $prize = $game->getPrize();
        $prize->setIsConvertable(true);

        $user = $game->getPlayer();

        $gamePoints = [10, 12, 15];
        $expectedUserPoints = [15, 27, 42];

        foreach ($gamePoints as $i => $gamePoint) {
            // given
            $game->setStatus(Game::STATUS_APPROVED);
            $game->setPoints($gamePoint);

            // when
            $this->processor->processGamePrize($game);

            // then
            $this->assertEquals($user->getPoints(), $expectedUserPoints[$i]);
            $this->assertEquals($game->getStatus(), Game::STATUS_PROCESSED);
        }
    }

    public function testPrizeWithPointsIsProcessed(): void
    {
        // given
        $game = $this->createGame(Game::STATUS_APPROVED, Prize::TYPE_POINTS);
        $user = $game->getPlayer();

        $gamePoints = [10, 12, 15];
        $expectedUserPoints = [7, 9, 11];

        foreach ($gamePoints as $i => $gamePoint) {
            // given
            $game->setStatus(Game::STATUS_APPROVED);
            $game->setPoints($gamePoint);

            // when
            $this->processor->processGamePrize($game);

            // then
            $this->assertEquals($user->getPoints(), $expectedUserPoints[$i]);
            $this->assertEquals($game->getStatus(), Game::STATUS_PROCESSED);
        }
    }

    public function testProcessedGamePrizeIsNotProcessedMoreThanOnce(): void
    {
        $game = $this->createGame(Game::STATUS_PROCESSED, Prize::TYPE_POINTS);
        $user = $game->getPlayer();

        $this->expectException(\Exception::class);

        $this->processor->processGamePrize($game);
        $this->assertEquals($user->getPoints(), 5);
    }

    public function testWinGamePrizeIsNotProcessed(): void
    {
        $game = $this->createGame(Game::STATUS_WIN, Prize::TYPE_POINTS);
        $user = $game->getPlayer();

        $this->expectException(\Exception::class);

        $this->processor->processGamePrize($game);

        $this->assertEquals($user->getPoints(), 5);
        $this->assertEquals($game->getStatus(), Game::STATUS_WIN);
    }

    public function testLostGamePrizeIsNotProcessed(): void
    {
        $game = $this->createGame(Game::STATUS_LOST, Prize::TYPE_POINTS);
        $user = $game->getPlayer();

        $this->expectException(\Exception::class);

        $this->processor->processGamePrize($game);

        $this->assertEquals($user->getPoints(), 5);
        $this->assertEquals($game->getStatus(), Game::STATUS_LOST);
    }

    public function testRejectedGamePrizeIsNotProcessed(): void
    {
        $game = $this->createGame(Game::STATUS_REJECTED, Prize::TYPE_POINTS);
        $user = $game->getPlayer();

        $this->expectException(\Exception::class);

        $this->processor->processGamePrize($game);

        $this->assertEquals($user->getPoints(), 5);
        $this->assertEquals($game->getStatus(), Game::STATUS_REJECTED);
    }
}
