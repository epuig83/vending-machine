<?php

namespace App\Tests\Unit\Service;

use App\Entity\Item;
use App\Exception\ItemException;
use App\Repository\ItemRepository;
use App\Service\ItemService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemServiceTest extends TestCase
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ItemRepository|mixed|MockObject
     */
    protected $mockedItemRepository;

    public function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mockedItemRepository = $this->createMock(ItemRepository::class);
    }

    /**
     * @throws ItemException
     */
    public function testFindItemByName__returnItem(): void
    {
        $itemName = 'Juice';
        $item = new Item();
        $item->setName($itemName);
        $item->setAmount(4);

        $this->mockedItemRepository->expects($this->once())
            ->method('findOneAvailableByName')
            ->willReturn($item);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedItemRepository);

        $itemService = new ItemService($this->em);
        $result = $itemService->findItemByName($itemName);

        $this->assertEquals($item, $result);
    }

    /**
     * @throws ItemException
     */
    public function testFindItemByName__throwsNotFoundException(): void
    {
        $this->mockedItemRepository->expects($this->once())
            ->method('findOneAvailableByName')
            ->willReturn(null);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedItemRepository);

        $this->expectException(ItemException::class);
        $itemService = new ItemService($this->em);
        $result = $itemService->findItemByName('Soda');
    }

    public function testUpdateItemStatus(): void
    {
        $itemName = 'Juice';
        $item = new Item();
        $item->setName($itemName);
        $item->setAmount(4);

        $itemService = new ItemService($this->em);
        $itemService->updateItemStatus($item);

        $this->assertEquals(3, $item->getAmount());
    }

    /**
     * @throws ItemException
     */
    public function testUpdateItem(): void
    {
        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(4);
        $item->setAmount(12);

        $payload = ['price' => 5, 'amount' => 10];
        $itemService = new ItemService($this->em);
        $itemService->updateItem($item, $payload);

        $this->assertEquals('Soda', $item->getName());
        $this->assertEquals(5, $item->getPrice());
        $this->assertEquals(22, $item->getAmount());
    }

    /**
     * @throws ItemException
     */
    public function testUpdateItem__throwsNotValidParametersException(): void
    {
        $itemName = 'Soda';
        $item = new Item();
        $item->setName($itemName);
        $item->setPrice(4);
        $item->setAmount(12);

        $payload = ['price' => -5, 'amount' => 10];
        $itemService = new ItemService($this->em);
        $this->expectException(ItemException::class);
        $itemService->updateItem($item, $payload);
    }

    public function testStatus(): void
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

        $this->mockedItemRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($itemsCollection);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->mockedItemRepository);

        $itemService = new ItemService($this->em);
        $result = $itemService->status();

        $this->assertEquals($itemsCollection, $result);
    }
}