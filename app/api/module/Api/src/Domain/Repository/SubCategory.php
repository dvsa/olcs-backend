<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Api\Entity\System\SubCategory as Entity;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SubCategory
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategory extends AbstractRepository
{
    protected $entity = Entity::class;

    /** @var  \Dvsa\Olcs\Transfer\Query\SubCategory\GetList */
    private $query;

    /**
     * Fetch data
     *
     * @param QueryInterface $query       Query Builder
     * @param int            $hydrateMode Hidrate Mode
     *
     * @return \ArrayIterator|\Traversable
     */
    public function fetchList(QueryInterface $query, $hydrateMode = Query::HYDRATE_ARRAY)
    {
        $this->query = $query;

        return parent::fetchList($query, $hydrateMode);
    }

    /**
     * Attach filters to query
     *
     * @param QueryBuilder                                  $qb    Query Builder
     * @param \Dvsa\Olcs\Transfer\Query\SubCategory\GetList $query Http query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getIsTaskCategory') && !empty($query->getIsTaskCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isTask', ':isTaskCategory'))
                ->setParameter('isTaskCategory', $query->getIsTaskCategory() === 'Y');
        }

        if (method_exists($query, 'getIsDocCategory') && !empty($query->getIsDocCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isDoc', ':isDocCategory'))
                ->setParameter('isDocCategory', $query->getIsDocCategory() === 'Y');
        }

        if (method_exists($query, 'getIsScanCategory') && !empty($query->getIsScanCategory())) {
            $qb->andWhere($qb->expr()->eq('m.isScan', ':isScanCategory'))
                ->setParameter('isScanCategory', $query->getIsScanCategory() === 'Y');
        }

        if (method_exists($query, 'getCategory') && !empty($query->getCategory())) {
            $qb->andWhere($qb->expr()->eq('m.category', ':category'))
                ->setParameter('category', $query->getCategory());
        }
    }

    /**
     * Join table to query by conditions
     *
     * @param QueryBuilder $qb Query Builder
     *
     * @return void
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $expr = $qb->expr();

        if (
            method_exists($this->query, 'getIsDocCategory')
            && $this->query->getIsDocCategory() === 'Y'
        ) {
            $qb
                ->join(
                    Entities\Doc\DocTemplate::class,
                    'dct',
                    Query\Expr\Join::WITH,
                    $expr->andX(
                        $expr->eq('dct.category', $this->alias . '.category'),
                        $expr->eq('dct.subCategory', $this->alias . '.id')
                    )
                )
                ->join(Entities\Doc\Document::class, 'dc', Query\Expr\Join::WITH, $expr->eq('dc.id', 'dct.document'));

            $this->getQueryBuilder()->modifyQuery($qb);
        }
    }
}
