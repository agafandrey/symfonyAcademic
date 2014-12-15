<?php

namespace Academic\ProjectBundle\Tests\Functional\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\UserBundle\Entity\User;
use Academic\ProjectBundle\Entity\Project;
use Academic\ProjectBundle\Entity\Issue;

/**
 * Class IssueRepositoryTest
 * @package Academic\ProjectBundle\Tests\Functional\Entity\Repository
 */
class IssueRepositoryTest extends WebTestCase
{

    /** @var EntityManager */
    private $em;

    protected function setUp()
    {
        $client = $this->createClient();
        $this->em = $client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetOpenStatus()
    {
        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getOpenStatus();
        $this->assertNotNull($status->getId());
    }

    public function testGetClosedStatus()
    {
        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getClosedStatus();
        $this->assertNotNull($status->getId());
    }

    public function testGetInProgressStatus()
    {
        $status = $this->em->getRepository('AcademicProjectBundle:Issue')->getInProgressStatus();
        $this->assertNotNull($status->getId());
    }

    public function testResolutionResolved()
    {
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionResolved();
        $this->assertNotNull($resolution->getId());
    }

    public function testResolutionUnResolved()
    {
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionUnResolved();
        $this->assertNotNull($resolution->getId());
    }

    public function testResolutionReopened()
    {
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getResolutionReopened();
        $this->assertNotNull($resolution->getId());
    }

    public function testGetIssuePriorityByWeight()
    {
        $resolution = $this->em->getRepository('AcademicProjectBundle:Issue')->getIssuePriorityByWeight(1);
        $this->assertNotNull($resolution->getId());
    }

    public function testGetAssigneeIssues()
    {
        $this->em->getConnection()->beginTransaction();

        $user = new User();
        $this->assertEmpty(
            $this->em->getRepository('AcademicUserBundle:User')->loadUserByUsername('testName')->getId()
        );
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->persist($user);

        $this->assertEmpty($this->em->getRepository('AcademicProjectBundle:Project')->getUserProjects($user->getId()));
        $this->assertEmpty($this->em->getRepository('AcademicProjectBundle:Issue')->getAssigneeIssues($user->getId()));

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $project->addParticipant($user);
        $this->em->persist($project);
        $this->em->flush();
        $this->assertNotNull($project->getId());

        $loadedProjects = $this->em->getRepository('AcademicProjectBundle:Project')->getUserProjects($user->getId());
        $this->assertSame($project, $loadedProjects[0]);

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
        $this->em->persist($issue);
        $this->em->flush();

        $this->assertNotNull($issue->getid());
        $this->assertEmpty($issue->getAssignee());

        $issue->setAssignee($user);
        $this->em->persist($issue);
        $this->em->flush();

        $this->assertContains(
            $issue,
            $this->em->getRepository('AcademicProjectBundle:Issue')->getAssigneeIssues($user->getId())
        );

        $this->em->getConnection()->rollback();
    }
}
