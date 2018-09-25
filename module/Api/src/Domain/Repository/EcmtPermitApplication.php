<?php

/**
 * Permit Application
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use \Doctrine\ORM\QueryBuilder;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;

/**
 * Permit Application
 */
class EcmtPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {

        if ($query->getStatus() !== null) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias .'.status', ':status')
            );
            $qb->setParameter('status', $query->getStatus());
        } else {
            $qb->addOrderBy($this->alias . '.' . $query->getSort(), $query->getOrder());
            $qb->andWhere($qb->expr()->in($this->alias . '.status', $query->getStatusIds()));

            if (method_exists($query, 'getOrganisation')) {
                $licences = $this->fetchLicenceByOrganisation($query->getOrganisation());
                $qb->andWhere($qb->expr()->in($this->alias . '.licence', $licences));
            }
        }

        if (method_exists($query, 'getLicence') && $query->getLicence() !== null) {
            $qb->andWhere($qb->expr()->in($this->alias . '.licence', $query->getLicence()));
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
     * Fetch under consideration application ids within a given stock
     *
     * @param int $stockId
     *
     * @return array
     */
    public function fetchUnderConsiderationApplicationIds($stockId)
    {
        $statement = $this->getEntityManager()->getConnection()->executeQuery(
            'select epa.id ' .
            'from irhp_permit_application ipa '.
            'inner join ecmt_permit_application epa on ipa.ecmt_permit_application_id = epa.id '.
            'inner join irhp_permit_window ipw on ipa.irhp_permit_window_id = ipw.id '.
            'where ipw.irhp_permit_stock_id = :stockId '.
            'and epa.status = :status',
            [
                'stockId' => $stockId,
                'status' => Entity::STATUS_UNDER_CONSIDERATION
            ]
        );

        return array_column($statement->fetchAll(), 'id');
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
