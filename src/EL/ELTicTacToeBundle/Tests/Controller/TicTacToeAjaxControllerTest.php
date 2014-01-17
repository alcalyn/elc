<?php

namespace EL\ELTicTacToeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicTacToeAjaxControllerTest extends WebTestCase
{
    public function testTick()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tick');
    }

}
