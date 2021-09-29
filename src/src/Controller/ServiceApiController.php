<?php

namespace App\Controller;

use App\Exception\CoinException;
use App\Exception\ItemException;
use App\Service\VendingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceApiController extends AbstractController
{
    /**
     * @OA\Post(tags={"Manager"}, summary="Update coin amount of the vending machine.",
     *      @OA\Parameter(
     *          name="coin",
     *          in="path",
     *          required=true,
     *          description="The coin value."
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Update coin amount."
     *      )
     * )
     *
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
     */
    public function putCoin(Request $request, VendingService $vendingService): Response
    {
        try {
            $payload = json_decode($request->getContent(), true);
            $vendingService->updateCoin($request->get('coin'), $payload);
            return $this->json([
                'success' => true,
                'message' => 'Coin amount has been updated to vending machine successfully.'
            ], Response::HTTP_CREATED);
        } catch(CoinException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }

    /**
     * @OA\Post(tags={"Manager"}, summary="Update coin amount of the vending machine.",
     *      @OA\Parameter(
     *          name="name",
     *          in="path",
     *          required=true,
     *          description="The item name."
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Update item price and amount."
     *      )
     * )
     *
     * @param Request $request
     * @param VendingService $vendingService
     * @return Response
     */
    public function putItem(Request $request, VendingService $vendingService): Response
    {
        try {
            $payload = json_decode($request->getContent(), true);
            $vendingService->updateItem($request->get('name'), $payload);
            return $this->json([
                'success' => true,
                'message' => 'Item price and amount have been updated to vending machine successfully.'
            ], Response::HTTP_CREATED);
        } catch(ItemException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }

    /**
     * @OA\Post(tags={"Manager"}, summary="Show vending machine status.",
     *      @OA\Response(
     *          response=200,
     *          description="."
     *      )
     * )
     *
     * @param VendingService $vendingService
     * @return Response
     */
    public function getStatus(VendingService $vendingService): Response
    {
        return $this->json($vendingService->getStatus(), Response::HTTP_OK);
    }
}