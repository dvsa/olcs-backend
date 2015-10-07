<?php

/**
 * PublicationLink
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PublicationLink
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationLink extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param int $busRegId
     * @return array
     */
    public function fetchByBusRegId($busRegId)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.busReg', ':busReg')
        )->setParameter('busReg', $busRegId);

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getTransportManager')) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias . '.transportManager', ':transportManager')
            )->setParameter('transportManager', $query->getTransportManager());
        }

        if (method_exists($query, 'getLicence')) {
            $qb->andWhere(
                $qb->expr()->eq($this->alias . '.licence', ':licence')
            )->setParameter('licence', $query->getLicence());
        }
    }

    /**
     * @param QueryInterface $query
     * @return array
     */
    public function fetchSingleUnpublished(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.publication', ':byPublication'))
            ->setParameter('byPublication', $query->getPublication());

        $qb->andWhere($qb->expr()->eq($this->alias . '.publicationSection', ':byPublicationSection'))
            ->setParameter('byPublicationSection', $query->getPublicationSection());

        if (method_exists($query, 'getPi')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPi'))
                ->setParameter('byPi', $query->getPi());
        }

        if (method_exists($query, 'getBusReg')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.busReg', ':byBusReg'))
                ->setParameter('byBusReg', $query->getBusReg());
        }

        if (method_exists($query, 'getApplication')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':byApplication'))
                ->setParameter('byApplication', $query->getApplication());
        }

        if (method_exists($query, 'getLicence')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':byLicence'))
                ->setParameter('byLicence', $query->getLicence());
        }

        $this->getQueryBuilder()->modifyQuery($qb);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    /**
     * @param QueryInterface $query
     * @return array
     */
    public function fetchPreviousPublicationNo(QueryInterface $query)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('publication', 'p');

        $qb->andWhere($qb->expr()->eq($this->alias . '.trafficArea', ':byTrafficArea'))
            ->setParameter('byTrafficArea', $query->getTrafficArea());

        $qb->andWhere($qb->expr()->eq('p.pubType', ':byPubType'))
            ->setParameter('byPubType', $query->getPubType());

        $qb->andWhere($qb->expr()->lt('p.publicationNo', ':byPublicationNo'))
            ->setParameter('byPublicationNo', $query->getPublicationNo());

        if (method_exists($query, 'getPi')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPi'))
                ->setParameter('byPi', $query->getPi());
        }

        if (method_exists($query, 'getApplication')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':byApplication'))
                ->setParameter('byApplication', $query->getApplication());
        }

        if (method_exists($query, 'getLicence')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':byLicence'))
                ->setParameter('byLicence', $query->getLicence());
        }

        $qb->orderBy('p.publicationNo', 'DESC')
        ->setMaxResults(1);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }
}
