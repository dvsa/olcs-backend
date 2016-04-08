<?php

/**
 * LicenceStatusRule.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule as Entity;
use Doctrine\ORM\QueryBuilder;

/**
 * LicenceStatusRule Repo
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceStatusRule extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'lsr';

    /**
     * Fetch rules to set a licence to revoke, curtail or suspend
     *
     * @param string $date Date to check for rules, normally this will be now
     *
     * @return array
     */
    public function fetchRevokeCurtailSuspend($date)
    {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($doctrineQb)->withRefdata()
            ->with('licenceStatus')->with('licence', 'l')->with('l.status');

        $doctrineQb->andWhere($doctrineQb->expr()->isNull($this->alias .'.startProcessedDate'))
            ->andWhere($doctrineQb->expr()->isNull($this->alias .'.deletedDate'))
            ->andWhere($doctrineQb->expr()->lte($this->alias .'.startDate', ':startDate'));

        $doctrineQb->setParameter('startDate', $date);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch rules to set a licence to valid
     *
     * @param string $date Date to check for rules, normally this will be now
     *
     * @return array
     */
    public function fetchToValid($date)
    {
        $doctrineQb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($doctrineQb)->withRefdata()
            ->with('licenceStatus')
            ->with('licence', 'l')
            ->with('l.status');

        $doctrineQb->andWhere($doctrineQb->expr()->isNull($this->alias .'.endProcessedDate'))
            ->andWhere($doctrineQb->expr()->isNotNull($this->alias .'.endDate'))
            ->andWhere($doctrineQb->expr()->isNull($this->alias .'.deletedDate'))
            ->andWhere($doctrineQb->expr()->lte($this->alias .'.endDate', ':endDate'));

        $doctrineQb->setParameter('endDate', $date);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch for a licence.
     *
     * @param null $licence
     * @return array
     */
    public function fetchForLicence($licence = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter('licenceId', $licence->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Override to add additional data to the default fetchById() method
     * @param QueryBuilder $qb
     * @inheritdoc
     */
    protected function applyFetchJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with($this->alias . '.licence', 'l')
            ->with('l.decisions', 'd');
    }
}
