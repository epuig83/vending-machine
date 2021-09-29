<?php

namespace App\Service;

use App\Entity\Item;
use App\Exception\ItemException;
use Doctrine\ORM\EntityManagerInterface;

class ItemService
{
    const ITEM_DECREASE_VALUE = 1;

    /**
     * @Var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param string $name
     * @return Item|null
     * @throws ItemException
     */
    public function findItemByName(string $name): ?Item
    {
        $repository = $this->em->getRepository(Item::class);
        $item = $repository->findOneAvailableByName($name);

        if (!$item) {
            throw ItemException::notFoundMessage();
        }

        return $item;
    }

    /**
     * @param Item $item
     * @param array $payload
     * @throws ItemException
     */
    public function updateItem(Item $item, array $payload): void
    {
        if ($payload['price'] < 0 || $payload['amount'] < 0) {
            throw ItemException::notValidParametersMessage();
        }

        $item->setPrice(number_format($payload['price'], 2));
        $item->setAmount($item->getAmount() + $payload['amount']);
        $this->em->persist($item);
        $this->em->flush();
    }

    /**
     * @param Item $item
     */
    public function updateItemStatus(Item $item): void
    {
        $item->setAmount($item->getAmount() - self::ITEM_DECREASE_VALUE);
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function status(): array
    {
        return $this->em->getRepository(Item::class)->findAll();
    }
}