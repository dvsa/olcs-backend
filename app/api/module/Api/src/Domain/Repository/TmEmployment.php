<?php

/**
 * TmEmployment
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

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

        $dqb->andWhere($dqb->expr()->eq('te.transportManager', ':tmId'))
            ->setParameter('tmId', $tmId);

        return $dqb->getQuery()->getResult();
    }
}
