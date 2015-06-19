<?php
/**
 * Community Licence
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLic as CommunityLicDTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Community Licence
 */
class CommunityLic extends AbstractRepository
{
    protected $entity = CommunityLicEntity::class;

    /**
     * @param QueryBuilder $qb
     * @param CommunityLicDTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getStatuses() !== null) {
            $statuses = explode(',', $query->getStatuses());
            for ($i = 0; $i < count($statuses); $i++) {
                $qb->orWhere($qb->expr()->eq($this->alias . '.status', ':status' . $i));
                $qb->setParameter('status' . $i, $statuses[$i]);
            }
        }
        if ($query->getLicence() !== null) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'));
            $qb->setParameter('licence', $query->getLicence());
        }
    }

    public function fetchOfficeCopy($licenceId)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->andWhere($qb->expr()->eq($this->alias . '.issueNo', ':issueNo'))
            ->setParameter('licence', $licenceId)
            ->setParameter('issueNo', 0);
        $results = $qb->getQuery()->execute();
        $retv = null;
        if (count($results)) {
            $retv = $results[0];
        }
        return $retv;
    }

    public function fetchValidLicences($licence)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->neq($this->alias . '.issueNo', ':issueNo'))
            ->andWhere($qb->expr()->eq($this->alias . '.licence', ':licence'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq($this->alias . '.status', ':pending'),
                    $qb->expr()->eq($this->alias . '.status', ':active'),
                    $qb->expr()->eq($this->alias . '.status', ':withdrawn'),
                    $qb->expr()->eq($this->alias . '.status', ':suspended')
                )
            )
            ->setParameter('licence', $licence)
            ->setParameter('issueNo', 0)
            ->setParameter('pending', CommunityLicEntity::STATUS_PENDING)
            ->setParameter('active', CommunityLicEntity::STATUS_ACTIVE)
            ->setParameter('withdrawn', CommunityLicEntity::STATUS_WITHDRAWN)
            ->setParameter('suspended', CommunityLicEntity::STATUS_SUSPENDED)
            ->orderBy($this->alias . '.issueNo', 'ASC');
        return $qb->getQuery()->execute();
    }

    public function fetchLicencesByIds($ids)
    {
        $qb = $this->createQueryBuilder();
        $i = 1;
        foreach ($ids as $id) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.id', ':id' . $i));
            $qb->setParameter('id' . $i++, $id);
        }
        return $qb->getQuery()->execute();
    }

    public function hasOfficeCopy($licenceId, $ids)
    {
        $hasOfficeCopy = false;
        $officeCopy = $this->fetchOfficeCopy($licenceId);
        if ($officeCopy) {
            $officeCopyId = $officeCopy->getId();
            if (in_array($officeCopyId, $ids)) {
                $hasOfficeCopy = true;
            }
        }
        return $hasOfficeCopy;
    }
}
