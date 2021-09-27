<?php

namespace App\Controller;

use App\Service\VendingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendingApiController extends AbstractController
{
    public function insertCoin(Request $request, VendingService $vendingService): Response
    {
        $vendingService->insertCoin($request->get('coin'));
        return $this->json([], Response::HTTP_CREATED);
    }

    public function returnCoin(VendingService $vendingService): Response
    {
        $coins = $vendingService->returnCoin();
        return $this->json($coins, Response::HTTP_OK);
    }

    public function getCoinStatus(VendingService $vendingService): Response
    {
        $status = $vendingService->coinStatus();
        return $this->json($status, Response::HTTP_OK);
    }
}
