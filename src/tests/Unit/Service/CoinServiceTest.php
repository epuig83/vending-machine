<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use App\Repository\CoinRepository;
use App\Service\CoinService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CoinServiceTest extends TestCase
{

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var CoinRepository|mixed|MockObject
     */
    protected $mockedCoinRepository;

    public function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->mockedCoinRepository = $this->createMock(CoinRepository::class);
    }

    /**
     * @throws CoinException
     */
    public function testFindCoinByValue__ReturnCoin(): void
    {
        $coinValue = 0.25;
        $coin = new Coin();
        $coin->setValue($coinValue);


        $this->mockedCoinRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($coin);

        $this->managerRegistry->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $coinService = new CoinService($this->managerRegistry);
        $result = $coinService->findCoinByValue($coinValue);

        $this->assertEquals($coin, $result);
    }

    /**
     * @throws CoinException
     */
    public function testFindCoinByValue__ThrowsNotFoundException(): void
    {
        $this->mockedCoinRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->managerRegistry->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $this->expectException(CoinException::class);
        $coinService = new CoinService($this->managerRegistry);
        $result = $coinService->findCoinByValue(0.50);
    }
}