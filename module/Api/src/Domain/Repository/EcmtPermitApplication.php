<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Query\Permits\ExpireEcmtPermitApplications as ExpireEcmtPermitApplicationsQuery;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

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

    protected $applicationTableName = 'ecmt_permit_application';
    protected $applicationEntityName = 'ecmtPermitApplication';
    protected $permitsRequiredEntityAlias = 'epa';
    protected $linkTableName = 'ecmt_application_country_link';
    protected $linkTableApplicationIdName = 'ecmt_application_id';

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
     * Mark all applications without valid permits as expired
     *
     * @return void
     */
    public function markAsExpired()
    {
        $this->getDbQueryManager()->get(ExpireEcmtPermitApplicationsQuery::class)->execute([]);
    }
}
