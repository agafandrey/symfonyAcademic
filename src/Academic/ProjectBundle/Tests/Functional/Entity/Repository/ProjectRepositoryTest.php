<?php

namespace Academic\ProjectBundle\Tests\Functional\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\UserBundle\Entity\User;
use Academic\ProjectBundle\Entity\Project;
/**
 * Class ProjectRepositoryTest
 * @package Academic\ProjectBundle\Tests\Functional\Entity\Repository
 */
class ProjectRepositoryTest extends WebTestCase
{

    /** @var EntityManager */
    private $em;

    protected function setUp()
    {
        $client = $this->createClient();
        $this->em = $client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetUserProjects()
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

        $this->assertEmpty($this->em->getRepository('AcademicProjectBundle:Project')->getUserProjects($user->getId()));

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $project->addParticipant($user);
        $this->em->persist($project);
        $this->em->flush();

        $this->assertNotNull($project->getId());

        $loadedProjects = $this->em->getRepository('AcademicProjectBundle:Project')->getUserProjects($user->getId());
        $this->assertSame($project, $loadedProjects[0]);

        $this->em->getConnection()->rollback();
    }

    public function testGetNonParticipants()
    {
        $this->em->getConnection()->beginTransaction();

        $userOne = new User();
        $this->assertEmpty($this->em->getRepository('AcademicUserBundle:User')->loadUserByUsername('testName')->getId());
        $userOne->setUsername('testName');
        $userOne->setPassword('testName');
        $userOne->setFullname('testName');
        $userOne->setTimezone('America/Phoenix');
        $userOne->setEmail('testName@mail.com');

        $userTwo = new User();
        $this->assertEmpty($this->em->getRepository('AcademicUserBundle:User')->loadUserByUsername('testName2')->getId());
        $userTwo->setUsername('testName2');
        $userTwo->setPassword('testName2');
        $userTwo->setFullname('testName2');
        $userTwo->setTimezone('America/Phoenix');
        $userTwo->setEmail('testName2@mail.com');

        $this->em->persist($userOne);
        $this->em->persist($userTwo);

        $project = new Project();
        $project->setName('Test Project');
        $project->setSummary('Test Summary');
        $this->em->persist($project);
        $this->em->flush();

        $this->assertNotNull($project->getId());
        $this->assertNotContains($userOne, $project->getParticipant());
        $this->assertNotContains($userTwo, $project->getParticipant());

        $project->addParticipant($userOne);
        $this->em->persist($project);
        $this->em->flush();

        $this->assertContains($userOne, $project->getParticipant());
        $this->assertContains($userTwo, $this->em->getRepository('AcademicProjectBundle:Project')->getNonParticipants($project->getId()));
        $this->assertNotContains($userOne, $this->em->getRepository('AcademicProjectBundle:Project')->getNonParticipants($project->getId()));

        $this->em->getConnection()->rollback();
    }

}