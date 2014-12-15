<?php
// src/Academic/ProjectBundle/DataFixtures/ORM/LoadIssueResolutionData.php

namespace Academic\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Academic\ProjectBundle\Entity\IssueResolution;

class LoadIssueResolutionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueResolution = new IssueResolution();
        $issueResolution->setResolutionCode('REOPENED');
        $issueResolution->setLabel('Reopened');
        $manager->persist($issueResolution);

        $issueResolution = new IssueResolution();
        $issueResolution->setResolutionCode('RESOLVED');
        $issueResolution->setLabel('Resolved');
        $manager->persist($issueResolution);

        $issueResolution = new IssueResolution();
        $issueResolution->setResolutionCode('UNRESOLVED');
        $issueResolution->setLabel('Not Resolved');
        $manager->persist($issueResolution);

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
