<?php

/**
 * Cases
 */

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Pi
 */
final class Pi extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Pi\Pi';

    private $cases;

    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderInterface $queryBuilder,
        RepositoryInterface $cases
    )
    {
        parent::__construct($em, $queryBuilder);
        $this->cases = $cases;
    }

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
    public function fetchUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        $case = $this->cases->fetchUsingId($query, $hydrateMode, $version);

        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('agreedByTc')
            ->with('assignedTo')
            ->with('decidedByTc')
            ->with('reasons')
            ->with('decisions')
            ->with('piHearings');

        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byId'))
            ->setParameter('byId', $query->getId());

        $pi = $qb->getQuery()->getResult($hydrateMode);

        if (!empty($pi) && $hydrateMode === Query::HYDRATE_ARRAY) {
            $case['pi'] = $pi[0];
        }

        return $case;
    }
}
