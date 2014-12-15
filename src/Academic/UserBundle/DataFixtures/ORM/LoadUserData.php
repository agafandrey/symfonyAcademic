<?php
// src/Academic/UserBundle/DataFixtures/ORM/LoadUserData.php

namespace Academic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Academic\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setSalt(md5(uniqid()));

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user)
        ;

        $user->setPassword($encoder->encodePassword('admin', $user->getSalt()));
        $user->setEmail('admin@academic.com');
        $user->setRole($this->getReference('admin-role'));
        $user->setIsActive(1);
        $user->setFullName('Admin');
        $user->setTimezone('America/Chicago');

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
