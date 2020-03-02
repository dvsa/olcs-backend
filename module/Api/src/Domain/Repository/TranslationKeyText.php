<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\TranslationKeyText as Entity;

/**
 * Translation Key Text
 */
class TranslationKeyText extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch by translation key and language
     *
     * @param $translationKeyId
     * @param $languageId
     * @return null|Entity
     */
    public function fetchByTranslationKeyLanguage($translationKeyId, $languageId)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.translationKey', ':translationKey'))
            ->andWhere($qb->expr()->eq($this->alias . '.language', ':language'))
            ->setParameter('translationKey', $translationKeyId)
            ->setParameter('language', $languageId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }
}
