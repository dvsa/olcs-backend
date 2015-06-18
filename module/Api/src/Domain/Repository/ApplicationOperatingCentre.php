<?php

/**
 * ApplicationOperatingCentre
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as Entity;

/**
 * ApplicationOperatingCentre
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationOperatingCentre extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'aoc';

    /**
     * Fetch a list Application Operating Centres for an Application
     *
     * @param int $applicationId
     *
     * @return array
     */
    public function fetchByApplication($applicationId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with('operatingCentre', 'oc')
            ->with('oc.address');

        $dqb->andWhere($dqb->expr()->eq('aoc.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
    }
}
