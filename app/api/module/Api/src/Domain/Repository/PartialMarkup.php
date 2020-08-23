<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup as Entity;

/**
 * Partial Markup
 */
class PartialMarkup extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch by partial and language
     *
     * @param $partial
     * @param $languageId
     * @return null|Entity
     */
    public function fetchByParentLanguage($partialId, $languageId)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.partial', ':partial'))
            ->andWhere($qb->expr()->eq($this->alias . '.language', ':language'))
            ->setParameter('partial', $partialId)
            ->setParameter('language', $languageId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }
}
