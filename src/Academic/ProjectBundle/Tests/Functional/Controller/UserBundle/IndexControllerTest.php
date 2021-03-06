<?php

namespace Academic\ProjectBundle\Tests\Functional\Controller\UserBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IndexControllerTest
 * @package Academic\ProjectBundle\Tests\Functional\Controller\UserBundle
 * @dbIsolation
 */
class IndexControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'admin',
            )
        );
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
}
