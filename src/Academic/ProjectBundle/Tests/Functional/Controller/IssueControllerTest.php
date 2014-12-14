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
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueListTestUser');
        $preparedData['user']->setEmail('issueListTest@mail.com');

        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_list',
                array(
                    'project' => $preparedData['project']->getId()
                ),
                false);
        $crawler = $this->client->request('GET', $url);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$preparedData['issue']->getCode().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    public function testCreateIssue()
    {
        $this->em->getConnection()->beginTransaction();
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueCreateTestUser');
        $preparedData['user']->setEmail('issueCreateTest@mail.com');
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_create',
                array(
                    'project' => $preparedData['project']->getId()
                    ),
                false);

        $crawler = $this->client->request('GET', $url);
        $form = $crawler->selectButton('Save')->form();

        $form['form[type]'] = 'BUG';
        $form['form[summary]'] = 'test Issue Summary';
        $form['form[priority]'] = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1)->getId();
        $form['form[assignee]'] = $preparedData['user']->getId();
        $form['form[description]'] = 'test Description';

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode());

        $this->em->getConnection()->rollback();

    }

    public function testIssueProfile()
    {
        $this->em->getConnection()->beginTransaction();
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueProfileTestUser');
        $preparedData['user']->setEmail('issueProfileTest@mail.com');
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_profile',
                array(
                    'issue' => $preparedData['issue']->getId()
                ),
                false);
        $crawler = $this->client->request('GET', $url);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("'.$preparedData['issue']->getSummary().'")')->count()
        );

        $this->em->getConnection()->rollback();
    }

    public function testEditIssue()
    {
        $this->em->getConnection()->beginTransaction();
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueEditTestUser');
        $preparedData['user']->setEmail('issueEditTest@mail.com');
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_edit',
                array(
                    'issue' => $preparedData['issue']->getId()
                ),
                false);

        $crawler = $this->client->request('GET', $url);
        $form = $crawler->selectButton('Save')->form();

        $form['form[type]'] = 'BUG';
        $form['form[summary]'] = 'test Issue Summary Edited';
        $form['form[priority]'] = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1)->getId();
        $form['form[assignee]'] = $preparedData['user']->getId();
        $form['form[description]'] = 'test Description';

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode());

        $this->em->getConnection()->rollback();

    }

    public function testProcessComment()
    {
        $this->em->getConnection()->beginTransaction();
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueCommentTestUser');
        $preparedData['user']->setEmail('issueCommentTest@mail.com');
        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_profile',
                array(
                    'issue' => $preparedData['issue']->getId()
                ),
                false);

        $crawler = $this->client->request('POST', $url);
        $form = $crawler->selectButton('Add Comment')->form();

        $form['comment_body'] = 'TEST COMMENT';

        $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode());
        $this->assertContains( 'success', $result->getContent());

        $this->em->getConnection()->rollback();

    }

    public function testIssueStatus()
    {
        $this->em->getConnection()->beginTransaction();
        $preparedData = $this->prepareData();
        $preparedData['user']->setUsername('issueListTestUser');
        $preparedData['user']->setEmail('issueListTest@mail.com');

        $this->em->flush();

        $url = $this->client->getContainer()
            ->get('router')
            ->generate('issue_status',
                array(
                    'issue' => $preparedData['issue']->getId(),
                    'action' => 'close'
                ),
                false);
        $this->client->request('GET', $url);

        $result = $this->client->getResponse();
        $this->assertEquals( 200, $result->getStatusCode());

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
