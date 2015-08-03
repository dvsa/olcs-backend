<?php

/**
 * ApplicationOperatingCentre
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;

/**
 * ApplicationOperatingCentre
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationOperatingCentre extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'aoc';

    /**
     * Fetch a list Application Operating Centres for an Application
     *
     * @param int $applicationId
     *
     * @return array
     */
    public function fetchByApplication($applicationId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with('operatingCentre', 'oc')
            ->with('oc.address');

        $dqb->andWhere($dqb->expr()->eq('aoc.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
    }

    public function fetchByApplicationIdForOperatingCentres($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $qb->innerJoin('aoc.operatingCentre', 'oc');
        $qb->innerJoin('oc.address', 'oca');
        $qb->leftJoin('oca.countryCode', 'ocac');
        $qb->leftJoin('oc.complaints', 'occ', Join::WITH, $qb->expr()->eq('occ.status', ':complaintStatus'));
        $qb->setParameter('complaintStatus', Complaint::COMPLAIN_STATUS_OPEN);

        $qb->andWhere(
            $qb->expr()->eq('aoc.application', ':application')
        );
        $qb->setParameter('application', $applicationId);

        $qb->addSelect('oc');
        $qb->addSelect('oca');
        $qb->addSelect('ocac');
        $qb->addSelect('occ');

        $qb->orderBy('oca.id', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
