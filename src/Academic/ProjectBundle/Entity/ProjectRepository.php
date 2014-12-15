<?php

namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{
    public function getNonParticipants($projectId)
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicUserBundle:User', 'u')
            ->select('u')
            ->leftJoin('AcademicProjectBundle:Project', 'p', Join::WITH, 'u NOT MEMBER OF p.participant')
            ->where('p.id =:project')->setParameter('project', $projectId);

        return $qb->getQuery()->getResult();
    }

    public function getUserProjects($userId)
    {
        $qb =$this->getEntityManager()->createQueryBuilder();
        $qb
            ->from('AcademicProjectBundle:Project', 'p')
            ->select('p')
            ->leftJoin('AcademicUserBundle:User', 'u', Join::WITH, 'u MEMBER OF p.participant')
            ->where('u.id =:user')->setParameter('user', $userId);
        return $qb->getQuery()->getResult();
    }
}
