<?php
namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BookControllerTest extends WebTestCase
{
    public function testCreatedBookSuccess()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/books',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title" : "Guerrero Americano"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreatedBookFieldEmpty()
    {
        $client = static::createClient();

       $client->request(
            'POST',
            'api/books',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title" : ""}'
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testCreatedBookFormEmpty()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/books',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}