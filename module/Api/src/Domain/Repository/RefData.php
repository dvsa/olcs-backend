<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\RefData as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * class RefData
 */
class RefData extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Filter list
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getRefDataCategory()) {
            $qb->andWhere($qb->expr()->eq($this->alias .'.refDataCategoryId', ':category'))
                ->setParameter('category', $query->getRefDataCategory());
        }
        $qb->orderBy($this->alias. '.displayOrder');

        $q = $qb->getQuery();
        $q->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $query->getLanguage());
    }

    /**
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)->with('parent', 'p');
    }
}
