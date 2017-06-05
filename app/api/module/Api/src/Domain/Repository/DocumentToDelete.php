<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Doc\DocumentToDelete as Entity;

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
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
