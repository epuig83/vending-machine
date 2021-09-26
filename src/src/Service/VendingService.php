<?php

namespace App\Service;

use App\Exception\CoinException;
use App\Utils\CoinHelper;

class VendingService
{
    /**
     * @var CoinService
     */
    protected $coinService;

    /**
     * @var PocketService
     */
    protected $pocketService;

    /**
     * @var CoinHelper
     */
    protected $coinHelper;

    public function __construct(CoinService $coinService, PocketService $pocketService, CoinHelper $coinHelper)
    {
        $this->coinService = $coinService;
        $this->pocketService = $pocketService;
        $this->coinHelper = $coinHelper;
    }

    /**
     * @param string $coinString
     * @throws CoinException
     */
    public function insertCoin(string $coinString): void
    {
        $coinValue = $this->coinHelper->parseString($coinString);
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
}