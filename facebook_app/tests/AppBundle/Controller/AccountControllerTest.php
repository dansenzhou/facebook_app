<?php

namespace Test\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;


class AccountControllerTest extends WebTestCase
{

    public function testLoginWithFacebookAction()
    {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(200, $client);
        $this->assertSame(1, $crawler->filter('a')->count());
    }
}