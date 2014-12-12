<?php

namespace Academic\UserBundle\Tests\Functional\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest
 * @package Academic\TestBundle\Test\Functional\Controller
 */

class UserControllerTest extends WebTestCase
{
    protected $client;
    /** @var EntityManager */
    protected $connection;

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

    public function testUserlist()
    {
        $url = $this->client->getContainer()->get('router')->generate('user_list', array(), false);
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );
    }

    public function testCreateuser()
    {
        $url = $this->client->getContainer()->get('router')->generate('user_create', array(), false);

        $fileUrl = $this->client->getContainer()->get('router')->generate('index', array(), false) . 'apple-touch-icon.png';
        $repo = $this->client->getContainer()->get('doctrine')->getRepository('AcademicUserBundle:Role');
        $testRole = $repo->getOperatorRole();
        $crawler = $this->client->request('GET', $url);
        $this->client->followRedirects(true);
        $form = $crawler->selectButton('Save User')->form();
        $form['form[username]'] = 'testuser';
        $form['form[password]'] = 'testuser';
        $form['form[fullname]'] = 'Test User';
        $form['form[file]']->upload($fileUrl);
        $form['form[email]'] = 'testuser@mail.com';
        $form['form[role]']->select($testRole->getId());
        $form['form[timezone]']->select('America/Chicago');

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode()
        );

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

    public function testUseredit()
    {
        $repo = $this->client->getContainer()->get('doctrine')->getRepository('AcademicUserBundle:User');
        $testUser = $repo->loadUserByUsername('admin');
        $url = $this->client->getContainer()->get('router')->generate('user_edit', array('id' => $testUser->getId()), false);
        $fileUrl = $this->client->getContainer()->get('router')->generate('index', array(), false) . 'apple-touch-icon.png';
        $repo = $this->client->getContainer()->get('doctrine')->getRepository('AcademicUserBundle:Role');
        $testRole = $repo->getAdminRole();
        $crawler = $this->client->request('GET', $url);
        $this->client->followRedirects(true);
        $form = $crawler->selectButton('Save User')->form();
        $form['form[username]'] = 'admin';
        $form['form[fullname]'] = 'Admin Admin';
        $form['form[file]']->upload($fileUrl);
        $form['form[email]'] = 'admin@mail.com';
        $form['form[role]']->select($testRole->getId());
        $form['form[timezone]']->select('America/Chicago');
        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode()
        );
    }
}
