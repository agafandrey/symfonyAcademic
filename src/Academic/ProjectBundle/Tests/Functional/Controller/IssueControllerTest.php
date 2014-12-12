<?php

namespace Academic\ProjectBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\ProjectBundle\Entity\Project;
use Academic\ProjectBundle\Entity\Issue;
use Academic\UserBundle\Entity\User;


/**
 * Class IssueControllerTest
 * @package Academic\ProjectBundle\Tests\Functional\Controller
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp()
    {
        $this->client = static::createClient(
            array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => 'admin',
            )
        );
        $this->client->followRedirects(true);
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

    }

    public function testIssueList()
    {
        $this->em->getConnection()->beginTransaction();
        $user = new User();
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

        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getOpenStatus();
        $priority = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1);
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionUnResolved();
        $reporter = $user ;

        $issue = new Issue();
        $issue->setCode('Test Code');
        $issue->setSummary('Test Summary');
        $issue->setDescription('Test Description');
        $issue->setType('Test Type');
        $issue->setStatus($status);
        $issue->setPriority($priority);
        $issue->setResolution($resolution);
        $issue->setReporter($reporter);
        $issue->setProject($project);
        $issue->setAssignee($user);
        $this->em->persist($issue);
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_list',
                array(
                    'project' => $project->getId()
                ),
                false);
        $crawler = $this->client->request('GET', $url);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$issue->getCode().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    public function testCreateIssue()
    {
        /*$this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty($this->em->getRepository('AcademicUserBundle:User')->loadUserByUsername('testName')->getId());
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

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_create',
                array(
                    'project' => $project->getId()
                    ),
                false);

        $crawler = $this->client->request('GET', $url);
        $form = $crawler->selectButton('Save')->form();

        $form['form[type]'] = 'BUG';
        $form['form[summary]'] = 'test Issue Summary';
        $form['form[priority]'] = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1)->getId();
        $form['form[assignee]'] = $user->getId();
        $form['form[description]'] = 'test Description';

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode());

        $this->em->getConnection()->rollback();*/

    }

    public function testIssueProfile()
    {
        $this->em->getConnection()->beginTransaction();
        $user = new User();
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

        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getOpenStatus();
        $priority = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1);
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionUnResolved();
        $reporter = $user ;

        $issue = new Issue();
        $issue->setCode('Test Code');
        $issue->setSummary('Test Summary');
        $issue->setDescription('Test Description');
        $issue->setType('Test Type');
        $issue->setStatus($status);
        $issue->setPriority($priority);
        $issue->setResolution($resolution);
        $issue->setReporter($reporter);
        $issue->setProject($project);
        $issue->setAssignee($user);
        $this->em->persist($issue);
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_profile',
                array(
                    'issue' => $issue->getId()
                ),
                false);
        $crawler = $this->client->request('GET', $url);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$issue->getSummary().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    private function prepareData()
    {
        $user = new User();
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

        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getOpenStatus();
        $priority = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1);
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionUnResolved();
        $reporter = $user ;

        $issue = new Issue();
        $issue->setCode('Test Code');
        $issue->setSummary('Test Summary');
        $issue->setDescription('Test Description');
        $issue->setType('Test Type');
        $issue->setStatus($status);
        $issue->setPriority($priority);
        $issue->setResolution($resolution);
        $issue->setReporter($reporter);
        $issue->setProject($project);
        $issue->setAssignee($user);
        $this->em->persist($issue);

        return array('user' => $user, 'project' => $project, 'issue' => $issue);

    }

}
