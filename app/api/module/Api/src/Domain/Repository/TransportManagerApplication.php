<?php

/**
 * Transport Manager Application Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as Entity;

/**
 * Transport Manager Application Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerApplication extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'tma';

    /**
     * Get a list of transport manager application with contact details
     *
     * @param int $applicationId
     *
     * @return array TransportManagerApplication entities
     */
    public function fetchWithContactDetailsByApplication($applicationId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)->withRefdata();
        $dqb->join('tma.transportManager', 'tm')->addSelect('tm')
            ->join('tm.homeCd', 'hcd')->addSelect('hcd')
            ->join('hcd.person', 'p')->addSelect('p');

        $dqb->andWhere($dqb->expr()->eq('tma.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
    }
}
