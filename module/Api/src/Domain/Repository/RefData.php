<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * class RefData
 */
class RefData extends EntityRepository
{
    public function findAllByCategoryAndLanguage($category, $language)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select(['r', 'p']);
        $qb->where('r.refDataCategoryId = ?0');
        $qb->orderBy('r.displayOrder');
        $qb->leftJoin('r.parent', 'p');

        $qb->setParameters([$category]);

        $q = $qb->getQuery();
        $q->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language);

        return $q->getArrayResult();
    }

    public function findByIdentifierAndLanguage($id, $language)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select(['r', 'p']);
        $qb->where('r.id = ?0');
        $qb->orderBy('r.displayOrder');
        $qb->leftJoin('r.parent', 'p');

        $qb->setParameters([$id]);

        $q = $qb->getQuery();
        $q->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $q->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language);

        return $q->getArrayResult();
    }
}
