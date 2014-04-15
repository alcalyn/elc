<?php

namespace EL\AwaleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AwaleControllerControllerTest extends WebTestCase
{
    public function testRefresh()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/refresh');
    }

    public function testPlay()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/play');
    }

}
