<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Entity\Item;
use App\Exception\CoinException;
use App\Exception\ItemException;
use App\Repository\CoinRepository;
use App\Repository\ItemRepository;
use App\Service\ChangeService;
use App\Service\CoinService;
use App\Service\ItemService;
use App\Service\PocketService;
use App\Service\VendingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class VendingServiceTest extends TestCase
{
    /**
     * @var CoinService|mixed|MockObject
     */
    protected CoinService $mockedCoinService;

    /**
     * @var PocketService|mixed|MockObject
     */
    protected PocketService $mockedPocketService;

    /**
     * @var ItemService|mixed|MockObject
     */
    protected ItemService $mockedItemService;

    /**
     * @var ChangeService|mixed|MockObject
     */
    protected ChangeService $mockedChangeService;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    public function setUp(): void
    {
        $this->mockedCoinService = $this->createMock(CoinService::class);
        $this->mockedPocketService = $this->createMock(PocketService::class);
        $this->mockedItemService = $this->createMock(ItemService::class);
        $this->mockedChangeService = $this->createMock(ChangeService::class);

        $session = new Session(new MockArraySessionStorage());
        $request = new Request([], [], [], [], [], [], [], json_encode([]));
        $request->setSession($session);
        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);
    }

    /**
     * @throws CoinException
     */
    public function testReturnCoins(): void
    {
        $coinValue = 0.25;
        $coin = new Coin();
        $coin->setValue($coinValue);
        $this->mockedCoinService->expects($this->once())
            ->method('findCoinByValue')
            ->willReturn($coin);

        $vendingService = new VendingService(
            $this->mockedCoinService,
            $this->mockedPocketService,
            $this->mockedItemService,
            $this->mockedChangeService
        );
        $vendingService->insertCoin((string) $coinValue);

        $this->mockedPocketService->expects($this->once())
            ->method('returnCoins')
            ->willReturn([$coinValue]);

        $this->assertEquals([$coinValue], $vendingService->returnCoin());
    }

    public function testGetCoinStatus(): void
    {
        $expected = [
            'money' => 1.15,
            'coins' => [0.25, 0.25, 0.25, 0.25, 0.10, 0.05]
        ];

        $coin = new Coin();
        $coin->setValue(0.25);

        $pocketService = new PocketService($this->requestStack);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin);

        $coin->setValue(0.10);
        $pocketService->insertCoin($coin);

        $coin->setValue(0.05);
        $pocketService->insertCoin($coin);

        $vendingService = new VendingService(
            $this->mockedCoinService,
            $pocketService,
            $this->mockedItemService,
            $this->mockedChangeService
        );

        $this->assertEquals($expected, $vendingService->coinStatus());
    }

    /**
     * @throws ItemException
     * @throws CoinException
     */
    public function testBuyItem__throwNotEnoughMoneyException(): void
    {
        $itemName = 'Juice';
        $itemPrice = 0.90;

        $item = new Item();
        $item->setName($itemName);
        $item->setPrice($itemPrice);
        $item->setAmount(4);

        $this->mockedItemService->expects($this->once())
            ->method('findItemByName')
            ->willReturn($item);

        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->mockedItemService);

        $changeService = new ChangeService($mockedEntityManager);
        $vendingService = new VendingService(
            $this->mockedCoinService,
            $this->mockedPocketService,
            $this->mockedItemService,
            $changeService
        );
        $this->expectException(CoinException::class);
        $vendingService->buyItem($itemName);
    }

    public function testGetItemStatus(): void
    {
        $itemName = 'Water';
        $item = new Item();
        $item->setName($itemName);
        $item->setAmount(4);

        $itemName2 = 'Juice';
        $item2 = new Item();
        $item2->setName($itemName);
        $item2->setAmount(7);

        $itemName3 = 'Soda';
        $item3 = new Item();
        $item3->setName($itemName);
        $item3->setAmount(2);

        $itemsCollection = [$itemName, $itemName2, $itemName3];

        $mockedItemRepository = $this->createMock(ItemRepository::class);
        $mockedItemRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($itemsCollection);

        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockedItemRepository);

        $itemService = new ItemService($mockedEntityManager);
        $vendingService = new VendingService(
            $this->mockedCoinService,
            $this->mockedPocketService,
            $itemService,
            $this->mockedChangeService
        );

        $this->assertEquals($itemsCollection, $vendingService->getItemStatus());
    }

    /**
     * @throws CoinException
     */
    public function testUpdateCoin(): void
    {
        $coinValue = 0.25;
        $coin = new Coin();
        $coin->setValue($coinValue);
        $coin->setAmount(5);

        $mockedCoinRepository = $this->createMock(CoinRepository::class);
        $mockedCoinRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($coin);

        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockedCoinRepository);

        $coinService = new CoinService($mockedEntityManager);
        $vendingService = new VendingService(
            $coinService,
            $this->mockedPocketService,
            $this->mockedItemService,
            $this->mockedChangeService
        );
        $payload = ['amount' => 5];
        $vendingService->updateCoin($coinValue, $payload);

        $this->assertEquals(10, $coin->getAmount());
    }

    /**
     * @throws ItemException
     */
    public function testUpdateItem(): void
    {
        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(1.55);
        $item->setAmount(12);

        $mockedItemRepository = $this->createMock(ItemRepository::class);
        $mockedItemRepository->expects($this->once())
            ->method('findOneAvailableByName')
            ->willReturn($item);

        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockedItemRepository);

        $itemService = new ItemService($mockedEntityManager);
        $vendingService = new VendingService(
            $this->mockedCoinService,
            $this->mockedPocketService,
            $itemService,
            $this->mockedChangeService
        );
        $payload = ['price' => 1.35, 'amount' => 7];
        $vendingService->updateItem($itemName, $payload);

        $this->assertEquals('Soda', $item->getName());
        $this->assertEquals(1.35, $item->getPrice());
        $this->assertEquals(19, $item->getAmount());
    }

    public function testGetStatus(): void
    {
        //pocketService
        $coin = new Coin();
        $coin->setValue(0.05);

        $coin2 = new Coin();
        $coin2->setValue(0.10);

        $pocketService = new PocketService($this->requestStack);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin2);

        //coin service
        $coinValue3 = 0.05;
        $coin3 = new Coin();
        $coin3->setValue($coinValue3);
        $coin3->setAmount(10);

        $coinValue4 = 0.1;
        $coin4 = new Coin();
        $coin4->setValue($coinValue4);
        $coin4->setAmount(10);

        $coinValue5 = 0.25;
        $coin5 = new Coin();
        $coin5->setValue($coinValue5);
        $coin5->setAmount(10);

        $coinValue6 = 1;
        $coin6 = new Coin();
        $coin6->setValue($coinValue6);
        $coin6->setAmount(10);

        $coinsCollection = [$coin3, $coin4, $coin5, $coin6];

        $mockedCoinRepository = $this->createMock(CoinRepository::class);
        $mockedCoinRepository->expects($this->any())
            ->method('findAll')
            ->willReturn($coinsCollection);

        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($mockedCoinRepository);
        $coinService = new CoinService($mockedEntityManager);

        //itemService
        $itemName = 'Water';
        $item = new Item();
        $item->setName($itemName);
        $item->setAmount(4);

        $itemName2 = 'Juice';
        $item2 = new Item();
        $item2->setName($itemName2);
        $item2->setAmount(7);

        $itemName3 = 'Soda';
        $item3 = new Item();
        $item3->setName($itemName3);
        $item3->setAmount(2);

        $itemsCollection = [$item, $item2, $item3];

        $mockedItemRepository = $this->createMock(ItemRepository::class);
        $mockedItemRepository->expects($this->any())
            ->method('findAll')
            ->willReturn($itemsCollection);
        $mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockedEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($mockedItemRepository);

        $itemService = new ItemService($mockedEntityManager);
        $vendingService = new VendingService(
            $coinService,
            $pocketService,
            $itemService,
            $this->mockedChangeService
        );

        $this->assertEquals(
            [
                "pocket" => [
                    "money" => $coin->getValue() + $coin2->getValue(),
                    "coins" => [
                        $coin->getValue(),
                        $coin2->getValue(),
                    ]
                ],
                "machine" => $coinsCollection,
                "items" => $itemsCollection,
            ],
            $vendingService->getStatus()
        );
    }
}