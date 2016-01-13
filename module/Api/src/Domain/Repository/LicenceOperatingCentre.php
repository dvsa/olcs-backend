<?php

/**
 * Licence Operating Centre Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as Entity;

/**
 * Licence Operating Centre Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceOperatingCentre extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'loc';

    /**
     * Fetch a list of Licence Operating Centres for a Licence
     *
     * @param int $licenceId
     *
     * @return array
     */
    public function fetchByLicence($licenceId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with('operatingCentre', 'oc')
            ->with('oc.address');

        $dqb->andWhere($dqb->expr()->eq('loc.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        return $dqb->getQuery()->getResult();
    }

    public function fetchByLicenceIdForOperatingCentres($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('loc.s4', 's4');
        $qb->innerJoin('loc.operatingCentre', 'oc');
        $qb->innerJoin('oc.address', 'oca');
        $qb->leftJoin('oca.countryCode', 'ocac');
        $qb->leftJoin('oc.complaints', 'occ', Join::WITH, $qb->expr()->eq('occ.status', ':complaintStatus'));
        $qb->setParameter('complaintStatus', ComplaintEntity::COMPLAIN_STATUS_OPEN);

        $qb->andWhere(
            $qb->expr()->eq('loc.licence', ':licence')
        );
        $qb->setParameter('licence', $licenceId);

        $qb->addSelect('s4');
        $qb->addSelect('oc');
        $qb->addSelect('oca');
        $qb->addSelect('ocac');
        $qb->addSelect('occ');

        $qb->orderBy('oca.id', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
