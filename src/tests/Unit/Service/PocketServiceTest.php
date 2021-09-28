<?php

namespace App\Tests\Unit\Service;

use App\Entity\Coin;
use App\Service\PocketService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class PocketServiceTest extends TestCase
{
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request([], [], [], [], [], [], [], json_encode([]));
        $request->setSession($session);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);
    }

    public function testInsertCoins(): void
    {
        $coin = new Coin();
        $coin->setValue(0.25);

        $coin2 = new Coin();
        $coin2->setValue(1);

        $pocketService = new PocketService($this->requestStack);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin2);

        $this->assertEquals([0.25, 1], $this->requestStack->getSession()->get('pocket'));
    }

    public function testReturnCoins(): void
    {
        $coin = new Coin();
        $coin->setValue(0.10);

        $coin2 = new Coin();
        $coin2->setValue(0.05);

        $pocketService = new PocketService($this->requestStack);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin2);

        $this->assertEquals([0.1, 0.05], $pocketService->returnCoins());
    }

    public function testStatus(): void
    {
        $coin = new Coin();
        $coin->setValue(0.05);

        $coin2 = new Coin();
        $coin2->setValue(0.10);

        $pocketService = new PocketService($this->requestStack);
        $pocketService->insertCoin($coin);
        $pocketService->insertCoin($coin2);

        $this->assertEquals(
            [
                'money' => 0.15,
                'coins' => [
                    0.05,
                    0.10
                ]
            ],
            $pocketService->status()
        );
    }

}