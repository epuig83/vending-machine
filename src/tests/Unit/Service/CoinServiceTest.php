<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Exception\CoinException;
use App\Repository\CoinRepository;
use App\Service\CoinService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CoinServiceTest extends TestCase
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CoinRepository|mixed|MockObject
     */
    protected $mockedCoinRepository;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
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

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $coinService = new CoinService($this->em);
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

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $this->expectException(CoinException::class);
        $coinService = new CoinService($this->em);
        $result = $coinService->findCoinByValue(0.50);
    }

    public function testUpdateCoinStatus(): void
    {
        $coinValue = 0.05;
        $coin = new Coin();
        $coin->setValue($coinValue);
        $coin->setAmount(10);

        $coinValue2 = 0.1;
        $coin2 = new Coin();
        $coin2->setValue($coinValue2);
        $coin2->setAmount(10);

        $coinValue3 = 0.25;
        $coin3 = new Coin();
        $coin3->setValue($coinValue3);
        $coin3->setAmount(10);

        $coinValue4 = 1;
        $coin4 = new Coin();
        $coin4->setValue($coinValue4);
        $coin4->setAmount(10);

        $coinsCollection = [$coin, $coin2, $coin3, $coin4];


        $this->mockedCoinRepository->expects($this->once())
            ->method('findBy')
            ->willReturn($coinsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $vendingCoins = [
            [
                'value' => 0.05,
                'amount' => 5
            ],
            [
                'value' => 0.10,
                'amount' => 8
            ],
            [
                'value' => 0.25,
                'amount' => 10
            ],
            [
                'value' => 1,
                'amount' => 10
            ]
        ];
        $pocketCoins = [0.05, 0.10];

        $coinService = new CoinService($this->em);
        $coinService->updateCoinStatus($vendingCoins, $pocketCoins);

        $this->assertEquals(6, $coin->getAmount());
        $this->assertEquals(9, $coin2->getAmount());
        $this->assertEquals(10, $coin3->getAmount());
        $this->assertEquals(10, $coin4->getAmount());
    }

    /**
     * @throws CoinException
     */
    public function testUpdateCoin(): void
    {
        $coinValue = 0.25;
        $amount = 4;

        $coin = new Coin();
        $coin->setValue($coinValue);
        $coin->setAmount($amount);

        $coinService = new CoinService($this->em);
        $coinService->updateCoin($coin, $amount);

        $this->assertEquals($coinValue, $coin->getValue());
        $this->assertEquals($amount + $amount, $coin->getAmount());
    }

    /**
     * @throws CoinException
     */
    public function testUpdateCoin__throwsNotValidAmountException(): void
    {
        $coinValue = 0.25;
        $amount = -1;

        $coin = new Coin();
        $coin->setValue($coinValue);
        $coin->setAmount($amount);

        $coinService = new CoinService($this->em);
        $this->expectException(CoinException::class);
        $coinService->updateCoin($coin, $amount);
    }

    public function testStatus(): void
    {
        $coinValue = 0.05;
        $coin = new Coin();
        $coin->setValue($coinValue);
        $coin->setAmount(10);

        $coinValue2 = 0.1;
        $coin2 = new Coin();
        $coin2->setValue($coinValue2);
        $coin2->setAmount(10);

        $coinValue3 = 0.25;
        $coin3 = new Coin();
        $coin3->setValue($coinValue3);
        $coin3->setAmount(10);

        $coinValue4 = 1;
        $coin4 = new Coin();
        $coin4->setValue($coinValue4);
        $coin4->setAmount(10);

        $coinsCollection = [$coin, $coin2, $coin3, $coin4];

        $this->mockedCoinRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($coinsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $coinService = new CoinService($this->em);
        $result = $coinService->status();

        $this->assertEquals($coinsCollection, $result);
    }
}