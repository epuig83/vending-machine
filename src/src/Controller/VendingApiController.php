<?php

namespace App\Controller;

use App\Exception\CoinException;
use App\Exception\ItemException;
use App\Service\VendingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendingApiController extends AbstractController
{
    /**
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
     * @throws \App\Exception\CoinException
     */
    public function insertCoin(Request $request, VendingService $vendingService): Response
    {
        try {
            $vendingService->insertCoin($request->get('coin'));
            return $this->json([
                'success' => true,
                'message' => 'Coin has been inserted to vending machine successfully'
            ], Response::HTTP_CREATED);
        } catch(CoinException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }

    /**
     * @param VendingService $vendingService
     * @return Response
     */
    public function returnCoin(VendingService $vendingService): Response
    {
        $coins = $vendingService->returnCoin();
        return $this->json($coins, Response::HTTP_OK);
    }

    /**
     * @param VendingService $vendingService
     * @return Response
     */
    public function getCoinStatus(VendingService $vendingService): Response
    {
        $status = $vendingService->coinStatus();
        return $this->json($status, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
     */
    public function getItemBuy(Request $request, VendingService $vendingService): Response
    {
        try{
            $change = $vendingService->buyItem($request->get('name'));
            return $this->json([
                'success' => true,
                'change' => $change
            ], Response::HTTP_OK);
        } catch (ItemException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }

    }
}
