<?php

/**
 * Partner
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Partner
 */
class Partner extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('address')
            ->byId($id);

        // limit by contact type
        $qb->andWhere($qb->expr()->eq($this->alias . '.contactType', ':contactType'));
        $qb->setParameter('contactType', Entity::CONTACT_TYPE_PARTNER);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.contactType', ':contactType'));
        $qb->setParameter('contactType', Entity::CONTACT_TYPE_PARTNER);

        $this->getQueryBuilder()->modifyQuery($qb)->withRefData()->with('address');
    }
}
