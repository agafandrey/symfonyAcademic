<?php

namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Academic\ProjectBundle\Entity\Issue;
use Doctrine\ORM\Query\Expr\Join;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository
{
    public function getOpenStatus()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueStatus', 's')
            ->select('s')
            ->where('s.status_code =:status_code')
            ->setParameter('status_code', 'OPENED');

        $issue_status = $qb->getQuery()->getSingleResult();
        return $issue_status;
    }

    public function getClosedStatus()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueStatus', 's')
            ->select('s')
            ->where('s.status_code =:status_code')
            ->setParameter('status_code', 'CLOSED');

        $issue_status = $qb->getQuery()->getSingleResult();
        return $issue_status;
    }

    public function getInProgressStatus()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueStatus', 's')
            ->select('s')
            ->where('s.status_code =:status_code')
            ->setParameter('status_code', 'IN_PROGRESS');

        $issue_status = $qb->getQuery()->getSingleResult();
        return $issue_status;
    }

    public function getResolutionResolved()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueResolution', 's')
            ->select('s')
            ->where('s.resolution_code =:resolution_code')
            ->setParameter('resolution_code', 'RESOLVED');

        $issue_resolution = $qb->getQuery()->getSingleResult();
        return $issue_resolution;
    }

    public function getResolutionUnResolved()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueResolution', 's')
            ->select('s')
            ->where('s.resolution_code =:resolution_code')
            ->setParameter('resolution_code', 'UNRESOLVED');

        $issue_resolution = $qb->getQuery()->getSingleResult();
        return $issue_resolution;
    }

    public function getResolutionClosed()
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:IssueResolution', 's')
            ->select('s')
            ->where('s.resolution_code =:resolution_code')
            ->setParameter('resolution_code', 'CLOSED');

        $issue_resolution = $qb->getQuery()->getSingleResult();
        return $issue_resolution;
    }

}
