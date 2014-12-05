<?php
// src/Academic/ProjectBundle/DataFixtures/ORM/LoadIssueStatusData.php

namespace Academic\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Academic\ProjectBundle\Entity\IssueStatus;

class LoadIssueStatusData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueStatus = new IssueStatus();
        $issueStatus->setStatusCode('OPENED');
        $issueStatus->setLabel('Opened');
        $manager->persist($issueStatus);

        $issueStatus = new IssueStatus();
        $issueStatus->setStatusCode('IN_PROGRESS');
        $issueStatus->setLabel('In Progress');
        $manager->persist($issueStatus);

        $issueStatus = new IssueStatus();
        $issueStatus->setStatusCode('CLOSED');
        $issueStatus->setLabel('Closed');
        $manager->persist($issueStatus);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}