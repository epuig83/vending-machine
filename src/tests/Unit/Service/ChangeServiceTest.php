<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Entity\Item;
use App\Exception\CoinException;
use App\Repository\CoinRepository;
use App\Service\ChangeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChangeServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CoinRepository|mixed|MockObject
     */
    protected CoinRepository $mockedCoinRepository;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mockedCoinRepository = $this->createMock(CoinRepository::class);
    }

    /**
     * @throws CoinException
     */
    public function testGetChange(): void
    {
        $coinsCollection = [
            [
                'value' => 0.25,
                'amount' => 23
            ],
            [
                'value' => 0.10,
                'amount' => 34
            ],
            [
                'value' => 0.05,
                'amount' => 15
            ]
        ];

        $this->mockedCoinRepository->expects($this->once())
            ->method('findCoinsSortedByValue')
            ->willReturn($coinsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(1.50);
        $item->setAmount(4);

        $changeService = new ChangeService($this->em);

        $this->assertEquals([0.25, 0.1, 0.1], $changeService->getChange($item, 1.95));
    }

    /**
     * @throws CoinException
     */
    public function testGetChange__throwsNotEnoughCoinsException(): void
    {
        $coinsCollection = [
            [
                'value' => 0.25,
                'amount' => 2
            ],
            [
                'value' => 0.10,
                'amount' => 1
            ],
            [
                'value' => 0.05,
                'amount' => 1
            ]
        ];

        $this->mockedCoinRepository->expects($this->once())
            ->method('findCoinsSortedByValue')
            ->willReturn($coinsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(1.50);
        $item->setAmount(4);

        $changeService = new ChangeService($this->em);
        $this->expectException(CoinException::class);
        $result = $changeService->getChange($item, 1.95);

    }

    public function testGetCoins(): void
    {
        $coinsCollection = [
            [
                'value' => 0.25,
                'amount' => 23
            ],
            [
                'value' => 0.10,
                'amount' => 34
            ],
            [
                'value' => 0.05,
                'amount' => 15
            ]
        ];

        $expectedCollection = [
            [
                'value' => 0.25,
                'amount' => 22
            ],
            [
                'value' => 0.10,
                'amount' => 32
            ],
            [
                'value' => 0.05,
                'amount' => 15
            ]
        ];

        $this->mockedCoinRepository->expects($this->once())
            ->method('findCoinsSortedByValue')
            ->willReturn($coinsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedCoinRepository);

        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(1.50);
        $item->setAmount(4);

        $changeService = new ChangeService($this->em);
        $result = $changeService->getChange($item, 1.95);
        $this->assertEquals($expectedCollection, $changeService->getCoinStatus());
    }
}