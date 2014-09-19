<?php

namespace Olcs\Db\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * class RefData
 * @package Olcs\Db\Entity\Repository
 */
class RefData extends EntityRepository
{
    public function findAllByCategoryAndLanguage($category, $language)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.refDataCategoryId = ?0');
        $qb->orderBy('r.displayOrder');

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
}
