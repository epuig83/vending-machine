<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VendingApiControllerTest extends WebTestCase
{
    public function testInsertCoin__returnSuccessTrue(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/coin/insert/0.25',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":true,"message":"Coin has been inserted to vending machine successfully"}',
            $client->getResponse()->getContent()
        );
    }

    public function testInsertCoin__returnSuccessFalse(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/coin/insert/0.50',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":false,"message":"The inserted coin is not valid!"}',
            $client->getResponse()->getContent()
        );
    }

    public function testReturnCoin(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/coin/return',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '[]',
            $client->getResponse()->getContent()
        );
    }

    public function testCoinStatus(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/coin/status',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '[]',
            $client->getResponse()->getContent()
        );
    }

    public function testGetItemBuy__returnSuccessTrue(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/coin/insert/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $client->request(
            'GET',
            '/api/item/buy/Water',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":true,"data":{"item":"Water","coins":[0.25,0.1]}}',
            $client->getResponse()->getContent()
        );
    }

    public function testGetItemBuy__returnSuccessFalse(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/item/buy/Water',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":false,"message":"Not enough money to buy selected item."}',
            $client->getResponse()->getContent()
        );
    }

    public function testGetItemStatus(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/item/status',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}