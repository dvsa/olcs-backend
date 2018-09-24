<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * IRHP Candidate Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermit extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Returns the count of candidate permits in the specified stock that have not been assigned a randomised score
     *
     * @param int $stockId
     *
     * @return int
     */
    public function getCountLackingRandomisedScore($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(icp.id)')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('ipa.status = \'' . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION . '\'')
            ->andWhere('icp.randomizedScore is null')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns the ids of candidate permits within the specified stock where the associated application has requested
     * the specified sector
     *
     * @param int $stockId
     * @param int sectorsId
     *
     * @return array
     */
    public function getScoreOrderedUnderConsiderationIdsBySector($stockId, $sectorsId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('icp.id')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('IDENTITY(ipa.sectors) = ?2')
            ->andWhere('ipa.status = \'' . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION . '\'')
            ->orderBy('icp.randomizedScore', 'DESC')
            ->setParameter(1, $stockId)
            ->setParameter(2, $sectorsId)
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Returns the count of candidate permits in the specified stock that are marked as successful and where the
     * associated application relates to a licence for the specified jurisdiction/devolved administration
     *
     * @param int $stockId
     * @param int $jurisdictionId
     *
     * @return int
     */
    public function getSuccessfulDaCount($stockId, $jurisdictionId)
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('count(icp.id)')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->innerJoin('ipa.licence', 'l')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('icp.successful = 1')
            ->andWhere('IDENTITY(l.trafficArea) = ?2')
            ->setParameter(1, $stockId)
            ->setParameter(2, $jurisdictionId)
            ->getQuery()
            ->getSingleScalarResult();

        if (is_null($result)) {
            return 0;
        }

        return $result;
    }

    /**
     * Returns the ids of candidate permits marked as unsuccessful within the specified stock where the associated
     * application is under consideration, ordered by randomised score descending
     *
     * @param int $stockId
     *
     * @return array
     */
    public function getUnsuccessfulScoreOrderedUnderConsiderationIds($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('icp.id')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('icp.successful = 0')
            ->andWhere('ipa.status = \'' . EcmtPermitApplication::STATUS_UNDER_CONSIDERATION. '\'')
            ->orderBy('icp.randomizedScore', 'DESC')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Returns the count of candidate permits in the specified stock marked as successful
     *
     * @param int $stockId
     *
     * @return int
     */
    public function getSuccessfulCount($stockId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(icp)')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('IDENTITY(ipw.irhpPermitStock) = ?1')
            ->andWhere('icp.successful = true')
            ->setParameter(1, $stockId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Marks a set of candidate permit ids as successful
     *
     * @param array $candidatePermitIds
     */
    public function markAsSuccessful($candidatePermitIds)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update(Entity::class, 'icp')
            ->set('icp.successful', 1)
            ->where('icp.id in (?1)')
            ->setParameter(1, $candidatePermitIds)
            ->getQuery();

        $query->execute();
    }

    /**
     * Retrieves the IrhpCandidateRecords that need to
     * have a randomised score set, for a given stock.
     *
     * @param int the Id of the IrhpPermitStock that the scoring will be for.
     * @return array a list of IrhpCandidatePermits
     * @TODO: Replace this query with the bundle & listFilter design pattern.
     */
    public function getIrhpCandidatePermitsForScoring($irhpPermitStockId)
    {
        $licenceTypes = [Licence::LICENCE_TYPE_RESTRICTED,  Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL];

        return $this->getEntityManager()->createQueryBuilder()
            ->select('icp')
            ->from(Entity::class, 'icp')
            ->innerJoin('icp.irhpPermitApplication', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->innerJoin('ipw.irhpPermitStock', 'ips')
            ->innerJoin('ipa.licence', 'l')
            ->where('ips.id = ?1')
            ->andWhere('ipa.status = ?2')
            ->andWhere('l.licenceType IN (?3)')
            ->setParameter(1, $irhpPermitStockId)
            ->setParameter(2, EcmtPermitApplication::STATUS_UNDER_CONSIDERATION)
            ->setParameter(3, $licenceTypes)
            ->getQuery()
            ->getResult();
    }
}
