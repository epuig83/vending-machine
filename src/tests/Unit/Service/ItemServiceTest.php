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
    public function testFindItemByName__ReturnItem(): void
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
    public function testFindItemByName__ThrowsNotFoundException(): void
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
}