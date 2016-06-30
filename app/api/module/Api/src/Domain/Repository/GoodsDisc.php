<?php

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Connection;

/**
 * Goods Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDisc extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'gd';

    public function fetchDiscsToPrint($niFlag, $licenceType)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->with('gd.licenceVehicle', 'lv')
            ->with('lv.licence', 'lvl')
            ->with('lvl.goodsOrPsv', 'lvlgp')
            ->with('lvl.licenceType', 'lvllt')
            ->with('lvl.trafficArea', 'lvlta')
            ->with('lv.vehicle', 'lvv')
            ->with('lv.application', 'lva')
            ->with('lva.licenceType', 'lvalt')
            ->with('lva.goodsOrPsv', 'lvagp');
        $this->addFilteringConditions($qb, $niFlag, $licenceType);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    protected function addFilteringConditions($qb, $niFlag, $licenceType)
    {
        $activeStatuses = [
            LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
            LicenceEntity::LICENCE_STATUS_GRANTED,
            LicenceEntity::LICENCE_STATUS_VALID,
            LicenceEntity::LICENCE_STATUS_CURTAILED,
            LicenceEntity::LICENCE_STATUS_SUSPENDED
        ];

        if ($niFlag == 'Y') {
            // for NI licences we don't check operator type
            $qb->andWhere(
                $qb->expr()->orX(
                    //isInterm = 1
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 1),
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType')
                    ),
                    // isInterm = 0
                    $qb->expr()->andX(
                        $qb->expr()->eq('lvlta.isNi', 1),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('applicationLicenceType', $licenceType);

            $qb->setParameter('licenceLicenceType', $licenceType);
        } else {
            // for non-NI licences we should check operator type as well
            $qb->andWhere(
                $qb->expr()->orX(
                    //isInterm = 1
                    $qb->expr()->andX(
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 1),
                        $qb->expr()->eq('lvagp.id', ':operatorType'),
                        $qb->expr()->eq('lvalt.id', ':applicationLicenceType')
                    ),
                    //isInterm = 0
                    $qb->expr()->andX(
                        // need to pick up discs from all traffic areas apart from NI
                        $qb->expr()->eq('lvlta.isNi', 0),
                        $qb->expr()->eq($this->alias. '.isInterim', 0),
                        $qb->expr()->eq('lvlgp.id', ':operatorType1'),
                        $qb->expr()->eq('lvllt.id', ':licenceLicenceType')
                    )
                )
            );
            $qb->andWhere($qb->expr()->isNull('gd.issuedDate'));
            $qb->andWhere($qb->expr()->isNull('gd.ceasedDate'));

            $qb->setParameter('operatorType', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('applicationLicenceType', $licenceType);

            $qb->setParameter('operatorType1', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);
            $qb->setParameter('licenceLicenceType', $licenceType);

        }
        $qb->andWhere($qb->expr()->in('lvl.status', ':activeStatuses'));
        $qb->setParameter('activeStatuses', $activeStatuses);
    }

    public function setIsPrintingOn($discIds)
    {
        $this->setIsPrinting(1, $discIds);
    }

    public function setIsPrintingOff($discIds)
    {
        $this->setIsPrinting(0, $discIds);
    }

    protected function setIsPrinting($type, $discIds)
    {
        return $this->getDbQueryManager()->get('Discs\GoodsDiscsSetIsPrinting')
            ->execute(
                ['isPrinting' => $type, 'ids' => $discIds],
                ['isPrinting' => \PDO::PARAM_INT, 'ids' => Connection::PARAM_INT_ARRAY]
            );
    }

    public function setIsPrintingOffAndAssignNumbers($discIds, $startNumber)
    {
        return $this->getDbQueryManager()->get('Discs\GoodsDiscsSetIsPrintingOffAndDiscNo')
            ->execute(
                ['ids' => $discIds, 'startNumber' => $startNumber],
                ['ids' => Connection::PARAM_INT_ARRAY, 'startNumber' => \PDO::PARAM_INT]
            );
    }

    /**
     * Cease all goods discs linked to active licence vehicles
     *
     * @param int $licenceId
     *
     * @return int Number of discs ceased
     */
    public function ceaseDiscsForLicence($licenceId)
    {
        return $this->getDbQueryManager()->get('LicenceVehicle\CeaseDiscsForLicence')
            ->execute(['licence' => $licenceId])
            ->rowCount();
    }

    /**
     * Cease all goods discs linked to active licence vehicles for an Application
     *
     * @param int $applicationId
     *
     * @return int Number of discs ceased
     */
    public function ceaseDiscsForApplication($applicationId)
    {
        return $this->getDbQueryManager()->get('LicenceVehicle\CeaseDiscsForApplication')
            ->execute(['application' => $applicationId])
            ->rowCount();
    }

    /**
     * Create a goods disc for each active licence vehicle
     *
     * @param int $licenceId
     *
     * @return int Number of discs created
     */
    public function createDiscsForLicence($licenceId)
    {
        return $this->getDbQueryManager()->get('LicenceVehicle\CreateDiscsForLicence')
            ->execute(['licence' => $licenceId])
            ->rowCount();
    }

    public function fetchDiscsToPrintMin($niFlag, $licenceType)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('gd.licenceVehicle', 'lv')
            ->leftJoin('lv.licence', 'lvl')
            ->leftJoin('lvl.goodsOrPsv', 'lvlgp')
            ->leftJoin('lvl.licenceType', 'lvllt')
            ->leftJoin('lvl.trafficArea', 'lvlta')
            ->leftJoin('lv.vehicle', 'lvv')
            ->leftJoin('lv.application', 'lva')
            ->leftJoin('lva.licenceType', 'lvalt')
            ->leftJoin('lva.goodsOrPsv', 'lvagp');
        $this->addFilteringConditions($qb, $niFlag, $licenceType);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Update existing discs for an application
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\Application $application
     */
    public function updateExistingGoodsDiscs(\Dvsa\Olcs\Api\Entity\Application\Application $application)
    {
        $this->getDbQueryManager()->get('Discs\CeaseGoodsDiscsForApplication')
            ->execute(
                [
                    'application' => $application->getId(),
                    'licence' => $application->getLicence()->getId(),
                ]
            );

        $this->getDbQueryManager()->get('Discs\CreateGoodsDiscs')
            ->execute(
                [
                    'application' => $application->getId(),
                    'licence' => $application->getLicence()->getId(),
                    'isCopy' => 0,
                ]
            );
    }
}
