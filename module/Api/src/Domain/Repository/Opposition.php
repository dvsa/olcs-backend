<?php

/**
 * Opposition
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Opposition\Opposition as Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Opposition
 */
class Opposition extends AbstractRepository
{
    protected $entity = Entity::class;

    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($em, $queryBuilder);
    }

    public function fetchUsingCaseId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('case')
            ->with('opposer', 'o')
            ->withPersonContactDetails('o.contactDetails', 'c')
            ->with('createdBy')
            ->with('lastModifiedBy')
            ->byId($query->getId());

        $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
            ->setParameter('byCase', $query->getCase());

        $result = $qb->getQuery()->getResult($hydrateMode);

        return $result[0];
    }

    /**
     * Override to add additional data to the default fetchList() method
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()
            ->with('application')
            ->with('case', 'ca')
            ->with('opposer', 'o')
            ->withPersonContactDetails('o.contactDetails');
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getCase()) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.case', ':byCase'))
                ->setParameter('byCase', $query->getCase());
        }
        if ($query->getLicence()) {
            $qb->andWhere($qb->expr()->eq($this->alias .'.licence', ':licence'))
                ->setParameter('licence', $query->getLicence());
        }
    }
}
