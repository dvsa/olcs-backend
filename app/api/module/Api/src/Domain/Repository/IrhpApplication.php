<?php

/**
 * IrhpApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Query\Permits\ExpireIrhpApplications as ExpireIrhpApplicationsQuery;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class IrhpApplication extends AbstractScoringRepository
{
    protected $entity = Entity::class;
    protected $alias = 'ia';

    protected $applicationTableName = 'irhp_application';
    protected $applicationEntityName = 'irhpApplication';
    protected $permitsRequiredEntityAlias = 'ipa';
    protected $linkTableName = 'irhp_application_country_link';
    protected $linkTableApplicationIdName = 'irhp_application_id';

    /**
     * @param int $licence
     *
     * @return array
     */
    public function fetchByLicence(int $licence)
    {
        return $this->fetchByX('licence', [$licence]);
    }

    /**
     * Fetch all applications by IRHP permit window id and status
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $windowId    IRHP Permit Window
     * @param array                                              $appStatuses List of app statuses
     *
     * @return array
     */
    public function fetchByWindowId($windowId, $appStatuses)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->innerJoin($this->alias.'.irhpPermitApplications', 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->where('ipw.id = :windowId')
            ->andWhere($qb->expr()->in($this->alias.'.status', ':appStatuses'))
            ->setParameter('windowId', $windowId)
            ->setParameter('appStatuses', $appStatuses);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch all applications in awaiting fee status
     *
     * @return array
     */
    public function fetchAllAwaitingFee()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('ia')
            ->from(Entity::class, 'ia')
            ->where('ia.status = :status')
            ->setParameter('status', IrhpInterface::STATUS_AWAITING_FEE)
            ->getQuery()->getResult();
    }

    /**
     * Fetch all valid roadworthiness applications
     *
     * @return array
     */
    public function fetchAllValidRoadworthiness()
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias . '.status', ':status'))
            ->andWhere($qb->expr()->in($this->alias . '.irhpPermitType', ':irhpPermitTypes'))
            ->setParameter('status', IrhpInterface::STATUS_VALID)
            ->setParameter('irhpPermitTypes', IrhpPermitType::CERTIFICATE_TYPES);

        return $qb->getQuery()->getResult();
    }

    /**
     * Mark all applications without valid permits as expired
     *
     * @return void
     */
    public function markAsExpired()
    {
        $this->getDbQueryManager()->get(ExpireIrhpApplicationsQuery::class)->execute([]);
    }
}
