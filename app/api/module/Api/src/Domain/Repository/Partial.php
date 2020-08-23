<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\Partial as Entity;
use Dvsa\Olcs\Transfer\Query\PartialMarkup\GetList;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Partial
 */
class Partial extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Apply List Filters
     *
     * @param QueryBuilder $qb Doctrine Query Builder
     * @param QueryInterface $query Http Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query instanceof GetList) {
            if ($query->getTranslationSearch() != null) {
                $qb->orWhere($this->alias . '.partialKey LIKE :translationSearch')
                    ->orWhere($this->alias . '.description LIKE :translationSearch')
                    ->orWhere($this->alias . '.prefix LIKE :translationSearch')
                    ->leftJoin($this->alias . '.partialMarkups', 'pms')
                    ->orWhere('pms.markup LIKE :translationSearch')
                    ->leftJoin($this->alias . '.partialTagLinks', 'ptl')
                    ->leftJoin('ptl.tag', 'tag')
                    ->orWhere('tag.tag LIKE :translationSearch')
                    ->setParameter('translationSearch', '%' . $query->getTranslationSearch() . '%');
            }

            if (!is_null($query->getCategory()) || !is_null($query->getSubCategory())) {
                $qb->leftJoin($this->alias . '.partialCategoryLinks', 'pcl');
                if ($query->getCategory() != null) {
                    $qb->andWhere('pcl.category = :category')
                        ->setParameter('category', $query->getCategory());
                }
                if ($query->getSubCategory() != null) {
                    $qb->andWhere('pcl.subCategory = :subCategory')
                        ->setParameter('subCategory', $query->getSubCategory());
                }
            }
        }
    }
}
