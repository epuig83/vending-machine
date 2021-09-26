<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use App\Service\CoinService;
use App\Service\PocketService;
use App\Service\VendingService;
use App\Utils\CoinHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VendingServiceTest extends TestCase
{
    /**
     * @var CoinService|mixed|MockObject
     */
    protected $coinService;

    /**
     * @var PocketService|mixed|MockObject
     */
    protected $pocketService;

    /**
     * @var CoinHelper|mixed|MockObject
     */
    protected $coinHelper;

    public function setUp(): void
    {
        $this->coinService = $this->createMock(CoinService::class);
        $this->pocketService = $this->createMock(PocketService::class);
        $this->coinHelper = $this->createMock(CoinHelper::class);
    }

    /**
     * @throws CoinException
     */
    public function testReturnCoins(): void
    {
        $coinValue = 0.25;
        $this->coinHelper->expects($this->once())
            ->method('parseString')
            ->willReturn($coinValue);

        $coin = new Coin();
        $coin->setValue($coinValue);
        $this->coinService->expects($this->once())
            ->method('findCoinByValue')
            ->willReturn($coin);

        $vendingService = new VendingService($this->coinService, $this->pocketService, $this->coinHelper);
        $vendingService->insertCoin((string) $coinValue);

        $this->pocketService->expects($this->once())
            ->method('returnCoins')
            ->willReturn([$coinValue]);

        $this->assertEquals([$coinValue], $vendingService->returnCoin());
    }
}