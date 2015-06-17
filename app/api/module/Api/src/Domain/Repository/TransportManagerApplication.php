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
        $this->joinTmContactDetails();

        $dqb->andWhere($dqb->expr()->eq($this->alias .'.application', ':applicationId'))
            ->setParameter('applicationId', $applicationId);

        return $dqb->getQuery()->getResult();
    }

    /**
     *
     * @param int $tmaId Transport Manager Application ID
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function fetchDetails($tmaId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with($this->alias .'.application', 'a')
            ->with($this->alias .'.otherLicences', 'ol')
            ->with('ol.role')
            ->with('a.goodsOrPsv', 'gop')
            ->with('a.licence')
            ->with('a.status')
            ->byId($tmaId);

        $this->joinTmContactDetails();

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Fetch TMA with operating centres
     *
     * @param int $tmaId
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function fetchWithOperatingCentres($tmaId)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with($this->alias .'.operatingCentres', 'oc')
            ->with('oc.address', 'add')
            ->with('add.countryCode')
            ->byId($tmaId);

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Join Trasport Manager, Contact Details and Person entities to the query
     */
    protected function joinTmContactDetails()
    {
        $this->getQueryBuilder()->with($this->alias .'.transportManager', 'tm')
            ->with('tm.homeCd', 'hcd')
            ->with('hcd.address', 'hadd')
            ->with('hadd.countryCode')
            ->with('hcd.person', 'hp')
            ->with('tm.workCd', 'wcd')
            ->with('wcd.address', 'wadd')
            ->with('wadd.countryCode');
    }
}
