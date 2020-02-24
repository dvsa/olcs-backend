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
        if ($query instanceof GetList && ($query->getTranslatedText() != null)) {
            $qb->select()
                ->innerJoin($this->alias . '.translationKeyTexts', 'tkt')
                ->andWhere('tkt.translatedText LIKE :translatedText')
                ->setParameter('translatedText', '%' . $query->getTranslatedText() . '%');
        }
    }
}
