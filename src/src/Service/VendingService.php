<?php

namespace App\Service;

use App\Entity\Item;
use App\Exception\CoinException;
use App\Exception\ItemException;
use App\Utils\CoinHelper;

class VendingService
{
    /**
     * @var CoinService
     */
    protected CoinService $coinService;

    /**
     * @var PocketService
     */
    protected PocketService $pocketService;

    /**
     * @var ItemService
     */
    protected ItemService $itemService;

    /**
     * @var ChangeService
     */
    protected ChangeService $changeService;

    /**
     * @var Item|null
     */
    private ?Item $selectedItem;

    public function __construct(
        CoinService $coinService,
        PocketService $pocketService,
        ItemService $itemService,
        ChangeService $changeService)
    {
        $this->coinService = $coinService;
        $this->pocketService = $pocketService;
        $this->itemService = $itemService;
        $this->changeService = $changeService;
        $this->selectedItem = null;
    }

    /**
     * @param string $coinString
     * @throws CoinException
     */
    public function insertCoin(string $coinString): void
    {
        $coinValue = CoinHelper::parseString($coinString);
        $coin = $this->coinService->findCoinByValue($coinValue);
        $this->pocketService->insertCoin($coin);
    }

    /**
     * @return array
     */
    public function returnCoin(): array
    {
        return $this->pocketService->returnCoins();
    }

    /**
     * @return array
     */
    public function coinStatus(): array
    {
        return $this->pocketService->status();
    }

    /**
     * @param string $item
     * @return array
     * @throws CoinException
     * @throws ItemException
     */
    public function buyItem(string $item): array
    {
        $this->selectedItem  = $this->itemService->findItemByName($item);
        if ($this->pocketService->getTotalAmount() < $this->selectedItem->getPrice()) {
            throw ItemException::notEnoughMoney();
        }

        $change = $this->changeService->getChange($this->selectedItem, $this->pocketService->getTotalAmount());
        $this->updateStates();

        return $change;

    }

    /**
     * @return array
     * @throws ItemException
     */
    public function getItemStatus(): array
    {
        return $this->itemService->status();
    }

    private function updateStates(): void
    {
        $this->coinService->updateCoinStatus(
            $this->changeService->getCoinStatus(),
            $this->pocketService->getCoins(),
        );

        $this->pocketService->empty();
        $this->itemService->updateItemStatus($this->selectedItem);

    }
}