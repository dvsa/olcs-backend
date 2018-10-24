<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use \Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\OrderedQueryInterface;
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
     * Fetch all under consideration applications
     *
     * @return array
     */
    public function fetchUnderConsiderationApplications()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('epa')
            ->from(Entity::class, 'epa')
            ->where('epa.status = ?1')
            ->setParameter(1, Entity::STATUS_UNDER_CONSIDERATION)
            ->getQuery()
            ->getResult();
    }
}
