<?php

/**
 * IrhpApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;
use \Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

class IrhpApplication extends AbstractRepository
{
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
}
