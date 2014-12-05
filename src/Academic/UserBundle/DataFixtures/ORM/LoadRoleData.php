<?php
// src/Academic/UserBundle/DataFixtures/ORM/LoadUserData.php

namespace Academic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Academic\UserBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $roleAdmin = new Role();
        $roleAdmin->setName('Administrator');
        $roleAdmin->setRole('ROLE_ADMIN');
        $manager->persist($roleAdmin);

        $role = new Role();
        $role->setName('Manager');
        $role->setRole('ROLE_MANAGER');
        $manager->persist($role);

        $role = new Role();
        $role->setName('Operator');
        $role->setRole('ROLE_USER');
        $manager->persist($role);

        $manager->flush();

        $this->addReference('admin-role', $roleAdmin);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}