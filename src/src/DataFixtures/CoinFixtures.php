<?php

namespace App\DataFixtures;

use App\Entity\Coin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoinFixtures extends Fixture
{

    public const COINS_DATA = [
        [
            'value' => 0.05,
            'amount' => 50
        ],
        [
            'value' => 0.10,
            'amount' => 40
        ],
        [
            'value' => 0.25,
            'amount' => 30
        ],
        [
            'value' => 1,
            'amount' => 20
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::COINS_DATA as $data) {
            $coin = new Coin();
            $coin->setValue($data['value']);
            $coin->setAmount($data['amount']);
            $manager->persist($coin);
        }

        $manager->flush();
    }
}
