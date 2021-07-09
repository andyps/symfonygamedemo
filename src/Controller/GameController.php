<?php

namespace App\Controller;

use App\Entity\Game;
use App\Services\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(private GameService $gameService)
    {
    }

    #[Route('/game', name: 'game')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $game = $this->gameService->createGame($this->getUser());
            return $this->redirectToRoute('viewGame', [ 'id' => $game->getId() ]);
        }
        return $this->render('game/index.html.twig');
    }

    #[Route('/game/{id}', name: 'viewGame')]
    public function viewGame(Game $game): Response
    {
        $gameStatus = $game->getStatus();
        $showResult = $gameStatus === Game::STATUS_WIN || $gameStatus === Game::STATUS_LOST;

        if ($showResult) {
            $potentialPoints = 0;
            $isConvertable = false;
            $prize = $game->getPrize();
            if ($prize && $prize->getIsConvertable()) {
                $isConvertable = true;
                $potentialPoints = $prize->getConvertionRate() * $game->getAmount();
            }

            return $this->render('game/gameResult.html.twig', [
                'game' => $game,
                'potentialPoints' => $potentialPoints,
                'isConvertable' => $isConvertable
            ]);
        }

        return $this->render('game/gameInfo.html.twig', [
            'game' => $game
        ]);
    }

    #[Route('/game/{id}/approve', name: 'approveGamePrize')]
    public function approveGame(Game $game): Response
    {
        $this->gameService->approve($game);
        $this->addFlash(
            'success',
            'You confirmed to receive your prize!'
        );
        return $this->redirectToRoute('viewGame', [ 'id' => $game->getId() ]);
    }

    #[Route('/game/{id}/reject', name: 'rejectGamePrize')]
    public function rejectGame(Game $game): Response
    {
        $this->gameService->reject($game);
        $this->addFlash(
            'success',
            'You rejected to receive your prize!'
        );
        return $this->redirectToRoute('viewGame', [ 'id' => $game->getId() ]);
    }

    #[Route('/game/{id}/convert', name: 'convertGamePrize')]
    public function convertGame(Game $game): Response
    {
        $this->gameService->convert($game);
        $this->addFlash(
            'success',
            'Receiving points confirmed!'
        );
        return $this->redirectToRoute('viewGame', [ 'id' => $game->getId() ]);
    }
}
