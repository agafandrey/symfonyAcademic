<?php

namespace Academic\UserBundle\Tests\Functional\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\UserBundle\Entity\User;

/**
 * Class UserRepositoryTest
 * @package Academic\UserBundle\Tests\Functional\Entity\Repository
 */
class UserRepositoryTest extends WebTestCase
{

    /** @var EntityManager */
    private $em;
    /** @var UserRepository */
    private $repo;

    protected function setUp()
    {
        $client = $this->createClient();
        $this->em = $client->getContainer()->get('doctrine')->getManagerForClass('AcademicUserBundle:User');
        $this->repo = $this->em->getRepository('AcademicUserBundle:User');
    }

    public function testLoadUserByUsername()
    {
        $user = new User();
        $this->assertNull($this->repo->loadUserByUsername('testName')->getId());
        $user->setUsername('testName');
        $user->setPassword('testName');
        $user->setFullname('testName');
        $user->setTimezone('America/Phoenix');
        $user->setEmail('testName@mail.com');

        $this->em->getConnection()->beginTransaction();

        $this->em->persist($user);
        $this->em->flush();
        $this->assertSame($user->getUsername(), $this->repo->loadUserByUsername('testName')->getUsername());

        $this->em->getConnection()->rollback();
    }

}