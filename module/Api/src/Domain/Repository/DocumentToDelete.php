<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete as Entity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * DocumentToDelete
 */
class DocumentToDelete extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Get a list of documents to delete
     *
     * @param int $limit Number of rows to return
     *
     * @return array
     */
    public function fetchListOfDocumentToDelete($limit)
    {
        $qb = $this->createQueryBuilder();
        $qb
            ->andWhere($qb->expr()->lt($this->alias . '.attempts', ':maxAttempts'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull($this->alias . '.processAfterDate'),
                $qb->expr()->lte($this->alias . '.processAfterDate', ':now')
            ));

        $qb->setParameter('maxAttempts', Entity::MAX_ATTEMPTS);
        $qb->setParameter('now', (new DateTime())->format("Y-m-d H:i:s"));

        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @param int $limit Number of rows to return
     *
     * @return array
     */
    public function fetchListOfDocumentToDeleteIncludingPostponed($limit)
    {
        $qb = $this->createQueryBuilder();
        $qb
            ->andWhere($qb->expr()->lt($this->alias . '.attempts', ':maxAttempts'));

        $qb->setParameter('maxAttempts', Entity::MAX_ATTEMPTS);

        $qb->orderBy($this->alias . '.processAfterDate');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
