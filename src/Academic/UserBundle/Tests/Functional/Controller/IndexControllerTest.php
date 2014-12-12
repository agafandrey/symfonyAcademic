<?php

namespace Academic\UserBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IndexControllerTest
 * @package Academic\UserBundle\Tests\Functional\Controller
 */
class IndexControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient(
            array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'admin',
            )
        );
        $this->client->followRedirects(true);
    }

    public function testIndex()
    {
        $url = $this->client->getContainer()->get('router')->generate('index', array(), false);
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );
    }

    public function testLogin()
    {
        $client = static::createClient();
        $url = $client->getContainer()->get('router')->generate('login', array(), false);

        $crawler = $client->request('GET', $url);
        $client->followRedirects(true);
        $form = $crawler->selectButton('login')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'admin';
        $client->submit($form);

        $result = $client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );
    }

    public function testLogout()
    {
        $url = $this->client->getContainer()->get('router')->generate('index', array(), false);
        $crawler = $this->client->request('GET', $url);
        $this->client->followRedirects(true);
        $link = $crawler->filter('a:contains("Log out")')->link();
        $this->client->click($link);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
