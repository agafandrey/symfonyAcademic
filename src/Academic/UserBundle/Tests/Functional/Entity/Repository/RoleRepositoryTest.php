<?php

namespace Academic\UserBundle\Tests\Functional\Entity\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Academic\UserBundle\Entity\Role;

/**
 * Class RoleRepositoryTest
 * @package Academic\UserBundle\Tests\Functional\Entity\Repository
 * @dbIsolation
 */
class RoleRepositoryTest extends WebTestCase
{

    /** @var EntityManager */
    private $em;
    /** @var RoleRepository */
    private $repo;

    protected function setUp()
    {
        $this->createClient();
        $this->em = self::$kernel->getContainer()->get('doctrine')->getManagerForClass('AcademicUserBundle:Role');
        $this->repo = $this->em->getRepository('AcademicUserBundle:Role');
    }

    public function testGetAdminRole()
    {
        $this->assertNotNull($this->repo->getAdminRole());
        $role = new Role();
        $role->setName('Admin Role');
        $role->setRole('ROLE_ADMIN');
        $this->assertSame($role->getRole(), $this->repo->getAdminRole()->getRole());
    }

    public function testGetManagerRole()
    {
        $this->assertNotNull($this->repo->getManagerRole());
        $role = new Role();
        $role->setName('Manager Role');
        $role->setRole('ROLE_MANAGER');
        $this->assertSame($role->getRole(), $this->repo->getManagerRole()->getRole());
    }

    public function testGetOperatorRole()
    {
        $this->assertNotNull($this->repo->getManagerRole());
        $role = new Role();
        $role->setName('Operator Role');
        $role->setRole('ROLE_USER');
        $this->assertSame($role->getRole(), $this->repo->getOperatorRole()->getRole());
    }

}