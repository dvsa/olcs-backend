<?php

/**
 * Cases
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Cases\Cases as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Cases
 */
class Cases extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch the default record by it's id plus licence information
     *
     * @param Query|QryCmd $query
     * @param int $hydrateMode
     * @param null $version
     * @return mixed
     * @throws Exception\NotFoundException
     * @throws Exception\VersionConflictException
     */
    public function fetchWithLicenceUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        parent::buildDefaultQuery($qb, $query->getId());

        $this->getQueryBuilder()
            ->with('licence', 'l')
            ->with('l.operatingCentres', 'loc')
            ->with('loc.operatingCentre', 'oc')
            ->with('oc.address');

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
     * Applies filters
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getTransportManager')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.transportManager', ':byTransportManager'))
                ->setParameter('byTransportManager', $query->getTransportManager());
        }

        if (method_exists($query, 'getLicence')) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':byLicence'))
                ->setParameter('byLicence', $query->getLicence());
        }
    }

    public function fetchExtended($caseId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('licence', 'l')
            ->with('application', 'a')
            ->with('transportManager', 'tm')
            ->byId($caseId);

        $res = $qb->getQuery()->getResult();
        if (!$res) {
            throw new Exception\NotFoundException('Resource not found');
        }
        return $res[0];
    }

    /**
     * Define composite columns as HIDDEN to enable ordering by case type
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     * @param array $compositeFields
     */
    public function buildDefaultListQuery(
        \Doctrine\ORM\QueryBuilder $qb,
        \Dvsa\Olcs\Transfer\Query\QueryInterface $query,
        $compositeFields = array()
    ) {
        // add calculated columns to allow ordering by them
        parent::buildDefaultListQuery($qb, $query, ['caseType']);

        $queryBuilderHelper = $this->getQueryBuilder();
        $queryBuilderHelper->with('caseType', 'ct');
        $qb->addSelect('CONCAT(ct.description, ' . $this->alias . '.id) as HIDDEN caseType');
    }
}
