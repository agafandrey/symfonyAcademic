<?php

namespace Academic\ProjectBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\ProjectBundle\Entity\Project;
use Academic\UserBundle\Entity\User;

/**
 * Class ProjectControllerTest
 * @package Academic\ProjectBundle\Tests\Functional\Controller
 * @dbIsolation
 */
class ProjectControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp()
    {
        $this->client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'admin',
            )
        );
        $this->client->followRedirects(true);
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testProjectList()
    {
        $url = $this->client->getContainer()->get('router')->generate('project_list', array(), false);
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );
    }

    public function testCreateProject()
    {
        $url = $this->client->getContainer()->get('router')->generate('project_create', array(), false);

        $crawler = $this->client->request('GET', $url);
        $this->client->followRedirects(true);
        $form = $crawler->selectButton('Save')->form();
        $form['form[name]'] = 'test Project name';
        $form['form[summary]'] = 'test Project Summary';

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals(200, $result->getStatusCode());

    }

    public function testProjectProfile()
    {
        $this->em->getConnection()->beginTransaction();

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $this->em->persist($project);
        $this->em->flush();

        $this->assertNotNull($project->getId());

        $url = $this->client->getContainer()->get('router')
            ->generate('project_profile', array('id' => $project->getId()), false);
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->em->getConnection()->rollback();
    }

    public function testProjectEdit()
    {
        $this->em->getConnection()->beginTransaction();

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $this->em->persist($project);
        $this->em->flush();

        $this->assertNotNull($project->getId());

        $url = $this->client->getContainer()->get('router')
            ->generate('project_edit', array('project' => $project->getId()), false);
        $crawler = $this->client->request('GET', $url);

        $form = $crawler->selectButton('Save')->form();
        $form['form[name]'] = 'test Project Name';
        $form['form[summary]'] = 'test Project Summary';
        $this->client->submit($form);
        $result = $this->client->getResponse();
        $this->assertEquals(200, $result->getStatusCode());

        $this->em->getConnection()->rollback();
    }

    public function testProjectParticipants()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty(
            $this->em->getRepository('AcademicUserBundle:User')
            ->loadUserByUsername('testName')
            ->getId()
        );
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $project->addParticipant($user);
        $this->em->persist($project);
        $this->em->flush();
        $this->assertNotNull($user->getId());
        $this->assertNotNull($project->getId());

        $url = $this->client->getContainer()->get('router')
            ->generate('project_participant', array('project' => $project->getId()), false);
        $crawler = $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$user->getFullname().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    public function testRemoveProjectParticipant()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty(
            $this->em->getRepository('AcademicUserBundle:User')
                ->loadUserByUsername('testName')
                ->getId()
        );
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $project->addParticipant($user);
        $this->em->persist($project);
        $this->em->flush();
        $this->assertNotNull($user->getId());
        $this->assertNotNull($project->getId());

        $this->assertContains($user, $project->getParticipant());


        $url = $this->client->getContainer()
            ->get('router')
            ->generate(
                'remove_participant',
                array(
                    'participant'   => $user->getId(),
                    'project'       => $project->getId()
                ),
                false
            );
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->assertNotContains($user, $project->getParticipant());

        $this->em->getConnection()->rollback();
    }

    public function testChoseParticipants()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty(
            $this->em->getRepository('AcademicUserBundle:User')
            ->loadUserByUsername('testName')
                ->getId()
        );
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $this->em->persist($project);
        $this->em->flush();
        $this->assertNotNull($user->getId());
        $this->assertNotNull($project->getId());

        $this->assertNotContains($user, $project->getParticipant());

        $url = $this->client->getContainer()->get('router')
            ->generate('choose_participant', array('project' => $project->getId()), false);
        $crawler = $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$user->getFullname().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    public function testAssignProjectParticipant()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty(
            $this->em->getRepository('AcademicUserBundle:User')
                ->loadUserByUsername('testName')
                ->getId()
        );
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $this->em->persist($project);
        $this->em->flush();
        $this->assertNotNull($user->getId());
        $this->assertNotNull($project->getId());

        $this->assertNotContains($user, $project->getParticipant());

        $url = $this->client->getContainer()
            ->get('router')
            ->generate(
                'assign_participant',
                array(
                    'participant[]'   => $user->getId(),
                    'project'       => $project->getId()
                ),
                false
            );
        $this->client->request('GET', $url);
        $result = $this->client->getResponse();
        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->assertContains($user, $project->getParticipant());

        $this->em->getConnection()->rollback();
    }
}
