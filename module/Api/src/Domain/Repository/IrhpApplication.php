<?php

/**
 * IrhpApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\Query\Permits\ExpireIrhpApplications as ExpireIrhpApplicationsQuery;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

class IrhpApplication extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'ia';

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
        if (method_exists($query, 'getStatusIds') && !empty($query->getStatusIds())) {
            $qb->andWhere($qb->expr()->in($this->alias . '.status', $query->getStatusIds()));
        }

        if (method_exists($query, 'getOrganisation') && $query->getOrganisation() !== null) {
            $licences = $this->fetchLicenceByOrganisation($query->getOrganisation());
            $qb->andWhere($qb->expr()->in($this->alias . '.licence', $licences));
        }
    }

    /**
     * Fetch a list of licences for an organisation
     *
     * @param int $organisationId The ID of the Organisation
     *
     * @return array
     */
    public function fetchLicenceByOrganisation($organisationId)
    {
        $licenceRows = $this->getEntityManager()->createQueryBuilder()
            ->select('l.id')
            ->from(LicenceEntity::class, 'l')
            ->where('l.organisation = ' . $organisationId)
            ->getQuery()
            ->execute();

        return array_column($licenceRows, 'id');
    }

    /**
     * @param int $licence
     *
     * @return array
     */
    public function fetchByLicence(int $licence)
    {
        return $this->fetchByX('licence', [$licence]);
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
     * Fetch all applications in awaiting fee status
     *
     * @return array
     */
    public function fetchAllAwaitingFee()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('ia')
            ->from(Entity::class, 'ia')
            ->where('ia.status = :status')
            ->setParameter('status', IrhpInterface::STATUS_AWAITING_FEE)
            ->getQuery()->getResult();
    }

    /**
     * Mark all applications without valid permits as expired
     *
     * @return void
     */
    public function markAsExpired()
    {
        $this->getDbQueryManager()->get(ExpireIrhpApplicationsQuery::class)->execute([]);
    }
}
