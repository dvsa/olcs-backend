<?php

/**
 * Irfo Psv Auth
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as Entity;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Irfo Psv Auth
 */
class IrfoPsvAuth extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch the default record by it's id
     *
     * @param int $id
     * @param int $hydrateMode
     * @param null $version
     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('irfoPsvAuthType')
            ->with('irfoPsvAuthNumbers')
            ->with('countrys')
            ->byId($id);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }

    /**
     * Fetch a list for an organisation
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     *
     * @return array
     */
    public function fetchByOrganisation($organisation)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.organisation', ':organisaion'))
            ->setParameter('organisaion', $organisation);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.organisation', ':byOrganisation'));
        $qb->setParameter('byOrganisation', $query->getOrganisation());

        $this->getQueryBuilder()->modifyQuery($qb)->with('irfoPsvAuthType');
    }
}
