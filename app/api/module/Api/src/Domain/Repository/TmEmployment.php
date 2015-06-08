<?php

/**
 * TmEmployment
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Tm\TmEmployment as Entity;

/**
 * TmEmployment
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TmEmployment extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'te';

    /**
     * Override default query, joining on contact details
     *
     * @param QueryBuilder $qb
     * @param int          $id
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        parent::buildDefaultQuery($qb, $id);
        return $this->getQueryBuilder()->with('contactDetails', 'cd')->with('cd.address');
    }

    /**
     * Fetch a list of Employments for a Transport Manager
     *
     * @param int $tmId
     *
     * @return array
     */
    public function fetchByTransportManager($tmId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->with('contactDetails', 'cd')
            ->with('cd.address')
            ->withRefdata();

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.transportManager', ':tmId'))
            ->setParameter('tmId', $tmId);

        return $dqb->getQuery()->getResult();
    }
}
