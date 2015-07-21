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

    public function fetchByBusRegId($busRegId)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq('busReg', ':busReg')
        )->setParameter('busReg', $busRegId);

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    public function fetchSingleUnpublished($publication, $pi, $publicationSection)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.publication', ':publication')
        )->setParameter('publication', $publication);

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.pi', ':pi')
        )->setParameter('pi', $pi);

        $qb->andWhere(
            $qb->expr()->eq($this->alias . '.publicationSection', ':publicationSection')
        )->setParameter('publicationSection', $publicationSection);

        $this->getQueryBuilder()->modifyQuery($qb);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            return $result;
        }

        return $result[0];
    }

    public function fetchPreviousPublicationNo($query)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('publication', 'p');

        if (method_exists($query, 'getPi')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPi'))
                ->setParameter('byPi', $query->getPi());
        }

        if (method_exists($query, 'getTrafficArea')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.trafficArea', ':byTrafficArea'))
                ->setParameter('byTrafficArea', $query->getTrafficArea());
        }

        if (method_exists($query, 'getPubType')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPubType'))
                ->setParameter('byPubType', $query->getPubType());
        }

        if (method_exists($query, 'getPublicationNo')) {
            $qb->andWhere($qb->expr()->lt('p.publicationNo', ':byPublicationNo'))
                ->setParameter('byPublicationNo', $query->getPublicationNo());
        }

        $qb->orderBy('p.publicationNo', 'DESC')
        ->setMaxResults(1);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if (empty($result)) {
            return $result;
        }

        return $result[0];
    }

    /**
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getPublication')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.publication', ':byPublication'))
                ->setParameter('byPublication', $query->getPublication());
        }

        if (method_exists($query, 'getPublicationSection')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.publicationSection', ':byPublicationSection'))
                ->setParameter('byPublicationSection', $query->getPublicationSection());
        }

        if (method_exists($query, 'getPi')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPi'))
                ->setParameter('byPi', $query->getPi());
        }

        if (method_exists($query, 'getPubStatus')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pubStatus', ':byPubStatus'))
                ->setParameter('byPubStatus', $query->getPubStatus());
        }

        if (method_exists($query, 'getPubType')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.pi', ':byPubType'))
                ->setParameter('byPubType', $query->getPubType());
        }

        if (method_exists($query, 'getTrafficArea')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.trafficArea', ':byTrafficArea'))
                ->setParameter('byTrafficArea', $query->getTrafficArea());
        }

        if (method_exists($query, 'getLicence')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':byLicence'))
                ->setParameter('byLicence', $query->getLicence());
        }

        if (method_exists($query, 'getApplication')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.application', ':byApplication'))
                ->setParameter('byApplication', $query->getApplication());
        }

        if (method_exists($query, 'getBusReg')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.busReg', ':byBusReg'))
                ->setParameter('byBusReg', $query->getBusReg());
        }
    }
}
