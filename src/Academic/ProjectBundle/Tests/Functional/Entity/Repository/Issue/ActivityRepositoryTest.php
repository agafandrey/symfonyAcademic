<?php

namespace Academic\ProjectBundle\Tests\Functional\Entity\Repository\Issue;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\UserBundle\Entity\User;
use Academic\ProjectBundle\Entity\Issue\Activity;
use Academic\ProjectBundle\Entity\Issue;
use Academic\ProjectBundle\Entity\Project;
/**
 * Class ActivityRepositoryTest
 * @package Academic\ProjectBundle\Tests\Functional\Entity\Repository\Issue
 */
class ActivityRepositoryTest extends WebTestCase
{

    /** @var EntityManager */
    private $em;

    protected function setUp()
    {
        $client = $this->createClient();
        $this->em = $client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetUserActivities()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty($this->em->getRepository('AcademicUserBundle:User')->loadUserByUsername('testName')->getId());
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $this->assertEmpty($this->em->getRepository('AcademicProjectBundle:Issue\Activity')->getUserActivities($user->getId()));

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');

        $this->em->persist($project);

        $issue = new Issue();

        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getOpenStatus();
        $priority = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1);
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionUnResolved();
        $reporter = $user ;
        $assignee = $user;

        $issue->setCode('Test Code');
        $issue->setSummary('Test Summary');
        $issue->setDescription('Test Description');
        $issue->setType('Test Type');
        $issue->setStatus($status);
        $issue->setPriority($priority);
        $issue->setResolution($resolution);
        $issue->setReporter($reporter);
        $issue->setAssignee($assignee);
        $issue->setProject($project);

        $this->em->persist($issue);

        $this->em->flush();

        $activity = new Activity();
        $activity->setIssue($issue);
        $activity->setUser($user);
        $activity->setEvent('testEvent');

        $this->em->persist($activity);
        $this->em->flush();
        $this->assertNotNull($activity->getId());
        $loadedActivities = $this->em->getRepository('AcademicProjectBundle:Issue\Activity')->getUserActivities($user->getId());
        $this->assertSame($activity, $loadedActivities[0]);

        $this->em->getConnection()->rollback();
    }

}