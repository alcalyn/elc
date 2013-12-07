<?php

namespace EL\PhaxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HelpControllerTest extends WebTestCase
{
    public function testDefault()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/default');
    }

}
