<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\TranslationKey as Entity;
use Dvsa\Olcs\Transfer\Query\TranslationKey\GetList;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Translation Key
 */
class TranslationKey extends AbstractRepository
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
                $qb->orWhere($this->alias . '.id LIKE :translationSearch')
                    ->orWhere($this->alias . '.description LIKE :translationSearch')
                    ->orWhere($this->alias . '.translationKey LIKE :translationSearch')
                    ->leftJoin($this->alias . '.translationKeyTexts', 'tkt')
                    ->orWhere('tkt.translatedText LIKE :translationSearch')
                    ->leftJoin($this->alias . '.translationKeyTagLinks', 'tktl')
                    ->leftJoin('tktl.tag', 'tag')
                    ->orWhere('tag.tag LIKE :translationSearch')
                    ->leftJoin($this->alias . '.translationKeyCategoryLinks', 'tkcat')
                    ->orWhere('tkcat.repository LIKE :translationSearch')
                    ->orWhere('tkcat.path LIKE :translationSearch')
                    ->setParameter('translationSearch', '%' . $query->getTranslationSearch() . '%');
            }

            if (!is_null($query->getCategory()) || !is_null($query->getSubCategory())) {
                $qb->leftJoin($this->alias . '.translationKeyCategoryLinks', 'tkcl');
                if ($query->getCategory() != null) {
                    $qb->andWhere('tkcl.category = :category')
                        ->setParameter('category', $query->getCategory());
                }
                if ($query->getSubCategory() != null) {
                    $qb->andWhere('tkcl.subCategory = :subCategory')
                        ->setParameter('subCategory', $query->getSubCategory());
                }
            }
        }
    }
}
