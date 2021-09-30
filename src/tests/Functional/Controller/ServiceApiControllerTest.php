<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ServiceApiControllerTest extends WebTestCase
{
    public function testPutCoin__returnSuccessTrue(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/service/coin/0.25',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'amount' => 10
            ])
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":true,"message":"Coin amount has been updated to vending machine successfully."}',
            $client->getResponse()->getContent()
        );
    }

    public function testPutCoin__returnSuccessFalse(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/service/coin/2',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'amount' => 10
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":false,"message":"The inserted coin is not valid!"}',
            $client->getResponse()->getContent()
        );
    }

    public function testPutItem__returnSuccessTrue(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/service/item/Soda',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'price' => 0.85,
                'amount' => 10
            ])
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":true,"message":"Item price and amount have been updated to vending machine successfully."}',
            $client->getResponse()->getContent()
        );
    }

    public function testPutItem__returnSuccessFalse(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/service/item/Coffee',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            '{"success":false,"message":"Item(s) not found!"}',
            $client->getResponse()->getContent()
        );
    }

    public function testGetStatus(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/service/status',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}