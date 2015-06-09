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
        $this->joinTransportManagerPerson($dqb);

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
    }

    /**
     *
     * @param int $tmaId Transport Manager Application ID
     *
     * @return Entity
     */
    public function fetchDetails($tmaId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with($this->alias .'.application', 'a')
            ->with($this->alias .'.operatingCentres')
            ->with($this->alias .'.otherLicences', 'ol')
            ->with('ol.role')
            ->with('a.goodsOrPsv', 'gop')
            ->with('a.licence')
            ->byId($tmaId);

        $this->joinTransportManagerPerson($dqb);

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Join Trasport Manager, Contact Details and Person entities to the query
     *
     * @param type $dqb
     */
    protected function joinTransportManagerPerson($dqb)
    {
        $dqb->join($this->alias .'.transportManager', 'tm')->addSelect('tm')
            ->join('tm.homeCd', 'hcd')->addSelect('hcd')
            ->join('hcd.person', 'p')->addSelect('p');
    }
}
