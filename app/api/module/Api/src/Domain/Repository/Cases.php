<?php

/**
 * Cases
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Cases
 */
/*final */class Cases extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Cases\Cases';

    /**
     * Fetch the default record by it's id
     *
     * @param Query|QryCmd $query
     * @param int $hydrateMode
     * @param null $version
     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchWithPiUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $case = $this->fetchUsingId($query, $hydrateMode, $version);
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->getEntityManager()->getRepository('\Dvsa\Olcs\Api\Entity\Pi\Pi')->createQueryBuilder('p');
        $this->getQueryBuilder()->modifyQuery($qb)
             ->withRefdata()
             ->with('agreedByTc')
             ->with('assignedTo')
             ->with('decidedByTc')
             ->with('reasons')
             ->with('decisions')
             ->with('piHearings');

        //reasons
        //hearings
        // -> presidingTc
        // -> presided by role (refdata)
        //decisions


        $qb->andWhere($qb->expr()->eq('p.case', ':byId'))
            ->setParameter('byId', $query->getId())
            ->setMaxResults(1);

        $pi = $qb->getQuery()->getResult();

        if (!empty($results) && $hydrateMode === Query::HYDRATE_ARRAY) {
            $case['pi'] = $pi[0];
        }

        return $case;
    }
}
