<?php

namespace App\Controller;

use App\Exception\CoinException;
use App\Exception\ItemException;
use App\Service\VendingService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendingApiController extends AbstractController
{
    /**
     * @OA\Post(tags={"Coin"}, summary="Insert coin.",
     *      @OA\Parameter(
     *          name="coin",
     *          in="path",
     *          required=true,
     *          description="The coin value."
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Inserted Coin"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Not Inserted Coin"
     *      )
     * )
     *
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
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
     * @OA\Get(tags={"Coin"}, summary="Return inserted coins.",
     *      @OA\Response(
     *          response=200,
     *          description="Return inserted coins."
     *      )
     * )
     *
     * @param VendingService $vendingService
     * @return Response
     */
    public function returnCoin(VendingService $vendingService): Response
    {
        $coins = $vendingService->returnCoin();
        return $this->json($coins, Response::HTTP_OK);
    }

    /**
     * @OA\Get(tags={"Coin"}, summary="Show inserted coins by value and its total amount.",
     *      @OA\Response(
     *          response=200,
     *          description="Return inserted coins and its total amount."
     *      )
     * )
     *
     * @param VendingService $vendingService
     * @return Response
     */
    public function getCoinStatus(VendingService $vendingService): Response
    {
        return $this->json($vendingService->coinStatus(), Response::HTTP_OK);
    }

    /**
     * @OA\Get(tags={"Item"}, summary="Select item to buy and get coins change.",
     *      @OA\Response(
     *          response=200,
     *          description="Return bought item and change."
     *      ),
     * )
     *
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
     */
    public function getItemBuy(Request $request, VendingService $vendingService): Response
    {
        try{
            $change = $vendingService->buyItem($request->get('name'));
            $vendingService->updateStates();

            return $this->json([
                'success' => true,
                'data' => $change
            ], Response::HTTP_OK);
        } catch (ItemException | CoinException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }

    }

    /**
     * @OA\Get(tags={"Item"}, summary="Get item status from vending machine.",
     *    @OA\Response(
     *        response=200,
     *        description="Return all items from vending machine."
     *    )
     * )
     *
     * @param VendingService $vendingService
     * @return Response
     */
    public function getItemStatus(VendingService $vendingService): Response
    {
        return $this->json($vendingService->getItemStatus(), Response::HTTP_OK);
    }
}
