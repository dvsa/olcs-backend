<?php

/**
 * Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
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
}
