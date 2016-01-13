<?php

/**
 * ApplicationOperatingCentre
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

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

    /**
     * Fetch a list Application Operating Centres by S4
     *
     * @param int $s4Id
     *
     * @return array
     */
    public function fetchByS4($s4Id)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata();

        $dqb->andWhere($dqb->expr()->eq('aoc.s4', ':s4Id'))
            ->setParameter('s4Id', $s4Id);

        return $dqb->getQuery()->getResult();
    }

    public function fetchByApplicationIdForOperatingCentres($applicationId)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('aoc.s4', 's4');
        $qb->innerJoin('aoc.operatingCentre', 'oc');
        $qb->innerJoin('oc.address', 'oca');
        $qb->leftJoin('oca.countryCode', 'ocac');
        $qb->leftJoin('oc.complaints', 'occ', Join::WITH, $qb->expr()->eq('occ.status', ':complaintStatus'));
        $qb->setParameter('complaintStatus', Complaint::COMPLAIN_STATUS_OPEN);

        $qb->andWhere(
            $qb->expr()->eq('aoc.application', ':application')
        );
        $qb->setParameter('application', $applicationId);

        $qb->addSelect('s4');
        $qb->addSelect('oc');
        $qb->addSelect('oca');
        $qb->addSelect('ocac');
        $qb->addSelect('occ');

        $qb->orderBy('oca.id', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function findCorrespondingLoc(Entity $aoc, LicenceEntity $licence)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq(
                'operatingCentre',
                $aoc->getOperatingCentre()
            )
        );

        $locs = $licence->getOperatingCentres()->matching($criteria);

        if ($locs->count() !== 1) {
            throw new \Exception('Expected 1 matching licence operating centre record, found: ' . $locs->count());
        }

        return $locs->first();
    }
}
