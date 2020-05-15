<?php

/**
 * Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Document extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchListForTmApplication($tmId, $applicationId)
    {
        return $this->fetchListForApplicationOrLicence($tmId, $applicationId, 'application');
    }

    public function fetchListForTmLicence($tmId, $licenceId)
    {
        return $this->fetchListForApplicationOrLicence($tmId, $licenceId, 'licence');
    }

    protected function fetchListForApplicationOrLicence($tmId, $id, $type)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.category', ':category'))
            ->andWhere($qb->expr()->eq($this->alias . '.subCategory', ':subCategory'))
            ->andWhere($qb->expr()->eq($this->alias . '.transportManager', ':transportManager'))
            ->andWhere($qb->expr()->eq($this->alias . '.' . $type, ':' . $type))
            ->setParameter('category', CategoryEntity::CATEGORY_TRANSPORT_MANAGER)
            ->setParameter('subCategory', CategoryEntity::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL)
            ->setParameter('transportManager', $tmId)
            ->setParameter($type, $id)
            ->orderBy($this->alias . '.id', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function fetchListForTm($id)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.category', ':category'))
            ->andWhere($qb->expr()->eq($this->alias . '.subCategory', ':subCategory'))
            ->andWhere($qb->expr()->eq($this->alias . '.transportManager', ':transportManager'))
            ->setParameter('category', CategoryEntity::CATEGORY_TRANSPORT_MANAGER)
            ->setParameter('subCategory', CategoryEntity::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION)
            ->setParameter('transportManager', $id)
            ->orderBy($this->alias . '.id', 'DESC');

        return $qb->getQuery()->execute();
    }

    /**
     * @param ApplicationEntity|LicenceEntity $entity
     * @return mixed
     */
    public function fetchUnlinkedOcDocumentsForEntity($entity)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->isNull('operatingCentre')
        );
        $criteria->andWhere(
            $criteria->expr()->eq(
                'category',
                $this->getCategoryReference(CategoryEntity::CATEGORY_APPLICATION)
            )
        );
        $criteria->andWhere(
            $criteria->expr()->eq(
                'subCategory',
                $this->getSubCategoryReference(CategoryEntity::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL)
            )
        );

        return $entity->getDocuments()->matching($criteria);
    }

    /**
     * Fetch a list of documents for a continuation detail ID
     *
     * @param int $continuationId Continuation ID
     * @param int $hydrationMode  Hydrate mode Query::HYDRATE_* constant
     *
     * @return array
     */
    public function fetchListForContinuationDetail($continuationId, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.continuationDetail', ':continuationDetail'))
            ->setParameter('continuationDetail', $continuationId)
            ->orderBy($this->alias . '.id', 'DESC');

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * Get Documents linked to a Statement
     *
     * @param int $statementId Statement ID
     *
     * @return array of Document entities
     */
    public function fetchListForStatement($statementId, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.statement', ':statementId'))
            ->setParameter('statementId', $statementId);

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * Get Documents linked to a Surrender
     *
     * @param int $surrenderId Surrender ID
     *
     * @return array of Document entities
     */
    public function fetchListForSurrender($surrenderId, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.surrender', ':surrenderId'))
            ->setParameter('surrenderId', $surrenderId);

        return $qb->getQuery()->getResult($hydrationMode);
    }

    /**
     * @param string $documentStoreId
     * @return array
     */
    public function fetchByDocumentStoreId($documentStoreId)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.identifier', ':documentStoreId'))
            ->setParameter('documentStoreId', $documentStoreId);

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Deletes the entity from the database. Document entity has soft-delete enabled by default and doesn't get removed
     * from the database.
     *
     * @param Entity $document
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function hardDelete(Entity $document)
    {
        $document->setDeletedDate(new DateTime());
        $this->delete($document);
    }
}
