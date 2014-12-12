<?php

namespace Academic\ProjectBundle\Tests\Functional\Controller\UserBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest
 * @package Academic\ProjectBundle\Tests\Functional\Controller\UserBundle
 * @dbIsolation
 */
class UserControllerTest extends WebTestCase
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

    public function testUserprofile()
    {
        $repo = $this->client->getContainer()->get('doctrine')->getRepository('AcademicUserBundle:User');
        $testUser = $repo->loadUserByUsername('admin');
        $url = $this->client->getContainer()->get('router')->generate('user_profile', array('id' => $testUser->getId()), false);
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );
    }

}
