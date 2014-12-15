<?php
// src/Academic/ProjectBundle/DataFixtures/ORM/LoadIssuePriorityData.php

namespace Academic\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Academic\ProjectBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issuePriority = new IssuePriority();
        $issuePriority->setWeight(1);
        $issuePriority->setLabel('Blocker');
        $manager->persist($issuePriority);

        $issuePriority = new IssuePriority();
        $issuePriority->setWeight(2);
        $issuePriority->setLabel('Critical');
        $manager->persist($issuePriority);

        $issuePriority = new IssuePriority();
        $issuePriority->setWeight(3);
        $issuePriority->setLabel('Minor');
        $manager->persist($issuePriority);

        $issuePriority = new IssuePriority();
        $issuePriority->setWeight(4);
        $issuePriority->setLabel('Trivial');
        $manager->persist($issuePriority);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
