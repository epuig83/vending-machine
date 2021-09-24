<?php

namespace App\DataFixtures;

use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture
{

    public const ITEMS_DATA = [
        [
            'name' => 'Water',
            'price' => 0.65,
            'amount' => 10
        ],
        [
            'name' => 'Juice',
            'price' => 1.00,
            'amount' => 10
        ],
        [
            'name' => 'Soda',
            'price' => 1.50,
            'amount' => 10
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::ITEMS_DATA as $data) {
            $item = new Item();
            $item->setName($data['name']);
            $item->setPrice($data['price']);
            $item->setAmount($data['amount']);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
