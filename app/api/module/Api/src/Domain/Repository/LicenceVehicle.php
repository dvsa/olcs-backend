<?php

/**
 * Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicle extends AbstractRepository
{
    protected $entity = Entity::class;

    public function createPaginatedVehiclesDataForApplicationQuery(QueryInterface $query, $applicationId, $licenceId)
    {
        $qb = $this->createFilteredQueryForLva($query);

        $specified = $query->getSpecified();

        if (isset($specified) && $specified === 'Y') {
            $this->filterByApplicationOrLicence($qb, $applicationId, $licenceId);
        } else {
            $qb->andWhere($qb->expr()->eq('m.application', ':application'));
            $qb->setParameter('application', $applicationId);
        }

        return $qb;
    }

    public function createPaginatedVehiclesDataForVariationQuery(QueryInterface $query, $applicationId, $licenceId)
    {
        $qb = $this->createFilteredQueryForLva($query);

        $this->filterByApplicationOrLicence($qb, $applicationId, $licenceId);

        return $qb;
    }

    public function createPaginatedVehiclesDataForLicenceQuery(QueryInterface $query, $licenceId)
    {
        $qb = $this->createFilteredQueryForLva($query, false);

        $qb->andWhere($qb->expr()->eq('m.licence', ':licence'));
        $qb->setParameter('licence', $licenceId);

        return $qb;
    }

    public function createPaginatedVehiclesDataForUnlicensedOperatorQuery(QueryInterface $query, $licenceId)
    {
        $qb = $this->createDefaultListQuery($query);

        $qb->innerJoin('m.vehicle', 'v');

        $qb->andWhere($qb->expr()->eq('m.licence', ':licence'));
        $qb->setParameter('licence', $licenceId);
        $qb->orderBy('m.createdOn', 'ASC');

        return $qb;
    }

    /**
     * @param ApplicationEntity|LicenceEntity $entity
     * @param $psvType
     * @return mixed
     */
    public function getPsvVehiclesByType($entity, $psvType, $includeRemoved = false)
    {
        $licenceVehicles = $this->getAllPsvVehicles($entity, $includeRemoved);

        $type = $this->getRefdataReference($psvType);

        /** @var Entity $licenceVehicle */
        foreach ($licenceVehicles as $licenceVehicle) {
            if ($licenceVehicle->getVehicle()->getPsvType() !== $type) {
                $licenceVehicles->removeElement($licenceVehicle);
            }
        }

        return $licenceVehicles;
    }

    /**
     * @param ApplicationEntity|LicenceEntity $entity
     */
    public function getAllPsvVehicles($entity, $includeRemoved = false)
    {
        $criteria = Criteria::create();
        if ($includeRemoved === false) {
            $criteria->andWhere(
                $criteria->expr()->isNull('removalDate')
            );
        }

        if ($entity instanceof ApplicationEntity) {
            $criteria->andWhere(
                $criteria->expr()->orX(
                    $criteria->expr()->eq('application', $entity),
                    $criteria->expr()->neq('specifiedDate', null)
                )
            );
            $entity = $entity->getLicence();
        } else {
            $criteria->andWhere(
                $criteria->expr()->neq('specifiedDate', null)
            );
        }

        $criteria->orderBy(['specifiedDate' => 'ASC']);

        return $entity->getLicenceVehicles()->matching($criteria);
    }

    public function fetchDuplicates(LicenceEntity $licence, $vrm)
    {
        $qb = $this->createQueryBuilder();

        $qb->innerJoin('m.vehicle', 'v')
            ->innerJoin('m.licence', 'l')
            // VRM matches
            ->andWhere($qb->expr()->eq('v.vrm', ':vrm'))
            // licence_vehicle.specified_date is not NULL;
            ->andWhere($qb->expr()->isNotNull('m.specifiedDate'))
            // licence_vehicle.removed_date is NULL;
            ->andWhere($qb->expr()->isNull('m.removalDate'))
            // licence_vehicle.warning_letter_seed_date is NULL
            ->andWhere($qb->expr()->isNull('m.warningLetterSeedDate'))
            // Not the current licence
            ->andWhere($qb->expr()->neq('l.id', ':licence'))
            // licence.goods_or_psv = Goods;
            ->andWhere($qb->expr()->eq('l.goodsOrPsv', ':goods'))
            // licence.status in (Curtailed, Valid, Suspended);
            ->andWhere(
                $qb->expr()->in(
                    'l.status',
                    [
                        LicenceEntity::LICENCE_STATUS_CURTAILED,
                        LicenceEntity::LICENCE_STATUS_VALID,
                        LicenceEntity::LICENCE_STATUS_SUSPENDED
                    ]
                )
            )
            ->setParameter('goods', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->setParameter('vrm', $vrm)
            ->setParameter('licence', $licence->getId());

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Generic filters for LVA
     *
     * @param QueryInterface $query
     * @return QueryBuilder
     */
    private function createFilteredQueryForLva(QueryInterface $query, $filterBySpecifiedDate = true)
    {
        $qb = $this->createDefaultListQuery($query);

        $qb->innerJoin('m.vehicle', 'v');
        $qb->leftJoin('m.interimApplication', 'in');

        $this->filterByDisc($qb, $query);
        $this->filterByVrm($qb, $query);
        $this->filterByRemovalDate($qb, $query);

        if ($filterBySpecifiedDate) {
            $this->filterBySpecifiedDate($qb, $query);
        } else {
            $this->filterSpecifiedOnly($qb);
        }

        return $qb;
    }

    /**
     * Filter by application or licence
     *
     * @param QueryBuilder $qb
     * @param $applicationId
     * @param $licenceId
     */
    private function filterByApplicationOrLicence(QueryBuilder $qb, $applicationId, $licenceId)
    {
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('m.application', ':application'),
                $qb->expr()->eq('m.licence', ':licence')
            )
        );
        $qb->setParameter('application', $applicationId);
        $qb->setParameter('licence', $licenceId);
    }

    /**
     * Filter the query to only show specified records
     *
     * @param QueryBuilder $qb
     */
    private function filterSpecifiedOnly(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->isNotNull('m.specifiedDate'));
    }

    /**
     * Filter a query that must either HAVE a disc or NOT HAVE a disc
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    private function filterByDisc(QueryBuilder $qb, QueryInterface $query)
    {
        $disc = $query->getDisc();

        if (isset($disc)) {

            if ($disc === 'Y') {
                $qb->innerJoin('m.goodsDiscs', 'gd');
                $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));
                $qb->andWhere($qb->expr()->isNotNull('gd.issuedDate'));
            }

            if ($disc === 'N') {
                $condition = 'gd.ceasedDate IS NULL AND gd.issuedDate IS NOT NULL';
                $qb->leftJoin('m.goodsDiscs', 'gd', Query\Expr\Join::WITH, $condition);
                $qb->andWhere($qb->expr()->isNull('gd.id'));
            }
        }
    }

    /**
     * Filter a query that must either HAVE specifiedDate or NOT HAVE specified date
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    private function filterBySpecifiedDate(QueryBuilder $qb, QueryInterface $query)
    {
        $specified = $query->getSpecified();

        if (isset($specified) && $specified === 'Y') {
            $this->filterSpecifiedOnly($qb);
            return;
        }

        if (isset($specified)) {
            $qb->andWhere($qb->expr()->isNull('m.specifiedDate'));
        }
    }

    /**
     * Filter a query to exclude removed discs
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    private function filterByRemovalDate(QueryBuilder $qb, QueryInterface $query)
    {
        $includeRemoved = $query->getIncludeRemoved();

        if (!$includeRemoved) {
            $qb->andWhere($qb->expr()->isNull('m.removalDate'));
        }
    }

    /**
     * Filter a query to show only VRMs starting with the $vrm string
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    private function filterByVrm(QueryBuilder $qb, QueryInterface $query)
    {
        $vrm = $query->getVrm();

        if (isset($vrm)) {
            $qb->andWhere($qb->expr()->like('v.vrm', ':vrm'));
            $qb->setParameter('vrm', $vrm . '%');
        }
    }
}
