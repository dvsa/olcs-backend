<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use \Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\RefData;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;

/**
 * Permit Application
 */
class EcmtPermitApplication extends AbstractRepository
{
    const VALID_APP_STATUS_IDS = [
        RefData::PERMIT_APP_STATUS_ISSUING,
        RefData::PERMIT_APP_STATUS_VALID
    ];

    const PENDING_APP_STATUS_IDS = [
        RefData::PERMIT_APP_STATUS_CANCELLED,
        RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
        RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
        RefData::PERMIT_APP_STATUS_WITHDRAWN,
        RefData::PERMIT_APP_STATUS_AWAITING_FEE,
        RefData::PERMIT_APP_STATUS_FEE_PAID,
        RefData::PERMIT_APP_STATUS_UNSUCCESSFUL,
    ];

    protected $entity = Entity::class;
    protected $alias = 'epa';

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getStatus') && $query->getStatus() !== null) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias .'.status', ':status')
            );
            $qb->setParameter('status', $query->getStatus());
        }

        if (method_exists($query, 'getStatusIds') && !empty($query->getStatusIds())) {
            $qb->andWhere($qb->expr()->in($this->alias . '.status', $query->getStatusIds()));
        }

        if (method_exists($query, 'getOrganisation') && $query->getOrganisation() !== null) {
            $licences = $this->fetchLicenceByOrganisation($query->getOrganisation());
            $qb->andWhere($qb->expr()->in($this->alias . '.licence', $licences));
        }

        if (method_exists($query, 'getLicence') && $query->getLicence() !== null) {
            $qb->andWhere($qb->expr()->in($this->alias . '.licence', $query->getLicence()));
        }

        if (method_exists($query, 'getOnlyIssued')) {
            $qb->andWhere($qb->expr()->in($this->alias . '.status', $query->getOnlyIssued() ? self::VALID_APP_STATUS_IDS : self::PENDING_APP_STATUS_IDS));
        }

        if ((method_exists($query, 'getSort') && $query->getSort() !== null)
            && (method_exists($query, 'getOrder') && $query->getOrder() !== null)) {
            $qb->addOrderBy($this->alias . '.' . $query->getSort(), $query->getOrder());
        }
    }

    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('fees');
    }

    /**
     * Fetch a list of licences for an organisation
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation Organisation
     *
     * @return array
     */
    public function fetchLicenceByOrganisation($organisationId)
    {
        $qbs = $this->getEntityManager()->createQueryBuilder()
            ->select('l.id')
            ->from(LicenceEntity::class, 'l')
            ->where('l.organisation = ' . $organisationId);

        $licenceIds = [];
        foreach ($qbs->getQuery()->execute() as $licence) {
            $licenceIds[] = $licence['id'];
        }
        return $licenceIds;
    }

    /**
     * Fetch all applications by IRHP permit window id and status
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $windowId    IRHP Permit Window
     * @param array                                              $appStatuses List of app statuses
     *
     * @return array
     */
    public function fetchByWindowId($windowId, $appStatuses)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->innerJoin($this->alias.'.irhpPermitApplications', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('ipw.id = :windowId')
            ->andWhere($qb->expr()->in($this->alias.'.status', ':appStatuses'))
            ->setParameter('windowId', $windowId)
            ->setParameter('appStatuses', $appStatuses);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch application ids within a stock that are awaiting scoring
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchApplicationIdsAwaitingScoring($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'select e.id from ecmt_permit_application e ' .
            'inner join licence as l on e.licence_id = l.id ' .
            'where e.id in (' .
            '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
            '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
            '    )' .
            ') ' .
            'and e.status = :status ' .
            'and l.licence_type in (:licenceType1, :licenceType2, :licenceType3) ' .
            'and l.status in (:licenceStatus1, :licenceStatus2, :licenceStatus3)',
            [
                'stockId' => $stockId,
                'status' => Entity::STATUS_UNDER_CONSIDERATION,
                'licenceType1' => LicenceEntity::LICENCE_TYPE_RESTRICTED,
                'licenceType2' => LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'licenceType3' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                'licenceStatus1' => LicenceEntity::LICENCE_STATUS_VALID,
                'licenceStatus2' => LicenceEntity::LICENCE_STATUS_SUSPENDED,
                'licenceStatus3' => LicenceEntity::LICENCE_STATUS_CURTAILED
            ]
        );

        return array_column($statement->fetchAll(), 'id');
    }

    /**
     * Fetch application ids within a stock that are both in scope and under consideration
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchInScopeUnderConsiderationApplicationIds($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'select e.id from ecmt_permit_application e ' .
            'where e.id in (' .
            '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
            '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
            '    )' .
            ') ' .
            'and e.in_scope = 1 ' .
            'and e.status = :status',
            [
                'stockId' => $stockId,
                'status' => Entity::STATUS_UNDER_CONSIDERATION
            ]
        );

        return array_column($statement->fetchAll(), 'id');
    }

    /**
     * Removes the existing scope from the specified stock id
     *
     * @param int $stockId
     */
    public function clearScope($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'update ecmt_permit_application e ' .
            'set e.in_scope = 0 ' .
            'where e.id in (' .
            '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
            '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
            '    )' .
            ')',
            ['stockId' => $stockId]
        );

        $statement->execute();
    }

    /**
     * Applies a new scope to the specified stock id, to include all applications that are under consideration and
     * and have a licence type of restricted or standard international
     *
     * @param int $stockId
     */
    public function applyScope($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'update ecmt_permit_application as e ' .
            'inner join licence as l on e.licence_id = l.id ' .
            'set e.in_scope = 1 ' .
            'where e.id in (' .
            '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
            '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
            '    )' .
            ') ' .
            'and e.status = :status ' .
            'and l.licence_type in (:licenceType1, :licenceType2, :licenceType3) ' .
            'and l.status in (:licenceStatus1, :licenceStatus2, :licenceStatus3)',
            [
                'stockId' => $stockId,
                'status' => Entity::STATUS_UNDER_CONSIDERATION,
                'licenceType1' => LicenceEntity::LICENCE_TYPE_RESTRICTED,
                'licenceType2' => LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'licenceType3' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                'licenceStatus1' => LicenceEntity::LICENCE_STATUS_VALID,
                'licenceStatus2' => LicenceEntity::LICENCE_STATUS_SUSPENDED,
                'licenceStatus3' => LicenceEntity::LICENCE_STATUS_CURTAILED
            ]
        );

        $statement->execute();
    }

    /**
     * Fetch a flat list of application to country associations within the specified stock
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchApplicationIdToCountryIdAssociations($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'select e.id as ecmtApplicationId, eacl.country_id as countryId ' .
            'from ecmt_application_country_link eacl ' .
            'inner join ecmt_permit_application as e on e.id = eacl.ecmt_application_id ' .
            'where e.id in (' .
            '    select ecmt_permit_application_id from irhp_permit_application where irhp_permit_window_id in (' .
            '        select id from irhp_permit_window where irhp_permit_stock_id = :stockId' .
            '    )' .
            ') ' .
            'and e.in_scope = 1 ',
            ['stockId' => $stockId]
        );

        return $statement->fetchAll();
    }
}
