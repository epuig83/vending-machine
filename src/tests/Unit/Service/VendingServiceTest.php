<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use App\Service\ChangeService;
use App\Service\CoinService;
use App\Service\ItemService;
use App\Service\PocketService;
use App\Service\VendingService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VendingServiceTest extends TestCase
{
    /**
     * @var CoinService|mixed|MockObject
     */
    protected CoinService $coinService;

    /**
     * @var PocketService|mixed|MockObject
     */
    protected PocketService $pocketService;

    /**
     * @var ItemService|mixed|MockObject
     */
    protected ItemService $itemService;

    /**
     * @var ChangeService|mixed|MockObject
     */
    protected ChangeService $changeService;

    public function setUp(): void
    {
        $this->coinService = $this->createMock(CoinService::class);
        $this->pocketService = $this->createMock(PocketService::class);
        $this->itemService = $this->createMock(ItemService::class);
        $this->changeService = $this->createMock(ChangeService::class);
    }

    /**
     * @throws CoinException
     */
    public function testReturnCoins(): void
    {
        $coinValue = 0.25;
        $coin = new Coin();
        $coin->setValue($coinValue);
        $this->coinService->expects($this->once())
            ->method('findCoinByValue')
            ->willReturn($coin);

        $vendingService = new VendingService(
            $this->coinService,
            $this->pocketService,
            $this->itemService,
            $this->changeService
        );
        $vendingService->insertCoin((string) $coinValue);

        $this->pocketService->expects($this->once())
            ->method('returnCoins')
            ->willReturn([$coinValue]);

        $this->assertEquals([$coinValue], $vendingService->returnCoin());
    }
}