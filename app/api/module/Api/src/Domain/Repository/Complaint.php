<?php

/**
 * Complaint
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Complaint
 */
class Complaint extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Overridden default query to return appropriate table joins
     * @param QueryBuilder $qb
     * @param int $id
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('complainantContactDetails', 'oc')
            ->with('oc.person')
            ->byId($id);
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query)
    {
        parent::buildDefaultListQuery($qb, $query);

        $this->getQueryBuilder()
            ->with('case', 'ca')
            ->with('complainantContactDetails', 'ccd')
            ->with('ccd.person')
            ->with('ocComplaints', 'occ')
            ->with('occ.operatingCentre', 'oc')
            ->with('oc.address');
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getCase()) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
                ->setParameter('byCase', $query->getCase());
        }
        if ($query->getIsCompliance() !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.isCompliance', ':isCompliance'))
                ->setParameter('isCompliance', $query->getIsCompliance());
        }
        if ($query->getLicence() !== null) {
            $qb->andWhere($qb->expr()->eq('ca.licence', ':licence'))
                ->setParameter('licence', $query->getLicence());
        }
    }
}
