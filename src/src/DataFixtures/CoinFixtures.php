<?php

namespace App\DataFixtures;

use App\Entity\Coin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoinFixtures extends Fixture
{

    public const COINS_DATA = [
        0.05 => 50,
        0.10 => 40,
        0.25 => 30,
        1 => 20
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::COINS_DATA as $value => $amount) {
            $coin = new Coin();
            $coin->setValue($value);
            $coin->setAmount($amount);
            $manager->persist($coin);
        }

        $manager->flush();
    }
}
