<?php

/**
 * Fee Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime as DateTimeExtended;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit as IrfoGvPermitEntity;

/**
 * Fee Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeType extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'ft';

    /**
     * @param RefDataEntity $feeType
     * @param RefDataEntity $goodsOrPsv
     * @param RefDataEntity $licenceType
     * @param \DateTime $date
     * @param mixed $trafficArea traffic area entity or id
     *
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     * @throws Exception\NotFoundException
     */
    public function fetchLatest(
        RefDataEntity $feeType,
        RefDataEntity $goodsOrPsv,
        RefDataEntity $licenceType = null,
        \DateTime $date = null,
        $trafficArea = null
    ) {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->withRefdata();

        if ($date === null) {
            // if not set, use today
            $date = new DateTimeExtended('now');
        } elseif (!$date instanceof \DateTime) {
            $date = new DateTimeExtended($date);
        }

        $effectiveOn = $date->format(\DateTime::W3C);

        $qb->andWhere($qb->expr()->eq('ft.feeType', ':feeType'))
            ->andWhere($qb->expr()->eq('ft.goodsOrPsv', ':goodsOrPsv'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ft.licenceType', ':licenceType'),
                    $qb->expr()->isNull('ft.licenceType')
                )
            )
            ->andWhere($qb->expr()->lte('ft.effectiveFrom', ':effectiveOn'));

        if ($trafficArea !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ft.trafficArea', ':trafficArea'),
                    $qb->expr()->isNull('ft.trafficArea')
                )
            )
            // Send the NULL values to the bottom
            ->orderBy('ft.trafficArea', 'DESC')
            ->setParameter('trafficArea', $trafficArea);

        } else {
            $qb->andWhere($qb->expr()->isNull('ft.trafficArea'));
        }

        $qb->addOrderBy('ft.effectiveFrom', 'DESC')
            ->setParameter('goodsOrPsv', $goodsOrPsv)
            ->setParameter('licenceType', $licenceType)
            ->setParameter('feeType', $feeType)
            ->setParameter('effectiveOn', $effectiveOn)
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            throw new Exception\NotFoundException('FeeType not found');
        }

        return $results[0];
    }

    /**
     * @param RefDataEntity $irfoFeeType
     * @param RefDataEntity $feeType
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     * @throws Exception\NotFoundException
     */
    public function fetchLatestForIrfo(RefDataEntity $irfoFeeType, RefDataEntity $feeType)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq('ft.feeType', ':feeType'));
        $qb->andWhere($qb->expr()->eq('ft.irfoFeeType', ':irfoFeeType'));

        $qb->addOrderBy('ft.effectiveFrom', 'DESC')
            ->setParameter('feeType', $feeType->getId())
            ->setParameter('irfoFeeType', $irfoFeeType->getId())
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            throw new Exception\NotFoundException('FeeType not found');
        }

        return $results[0];
    }

    /**
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     * @throws Exception\NotFoundException
     */
    public function fetchLatestForOverpayment()
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq('ft.feeType', ':feeType'));

        $qb->orderBy('ft.effectiveFrom', 'DESC')
            ->setParameter('feeType', Entity::FEE_TYPE_ADJUSTMENT)
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            throw new Exception\NotFoundException('FeeType not found');
        }

        return $results[0];
    }

    /**
     * Expected use cases:
     * application OR licence OR organisation is specified OR isMiscellaneous=1
     *
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getEffectiveDate()) {
            $date = new DateTimeExtended($query->getEffectiveDate());
        } else {
            $date = new DateTimeExtended('now');
        }

        $qb->andWhere(
            $qb->expr()->lte($this->alias.'.effectiveFrom', ':effectiveFrom')
        );
        $qb->setParameter('effectiveFrom', $date);

        $qb->addOrderBy('ftft.id', 'ASC'); // feeType.feeType.id
        $qb->addOrderBy($this->alias.'.effectiveFrom', 'DESC');

        // NOTE we can't do the required group by with DQL here so it's done in
        // the query handler

        if ($query->getIsMiscellaneous() !== null) {
            $qb->andWhere($this->alias.'.isMiscellaneous = :isMiscellaneous')
                ->setParameter('isMiscellaneous', $query->getIsMiscellaneous());
        }

        if ($query->getOrganisation()) {
            //  fee_type records where: is_miscellaneous = 0
            $qb->andWhere($this->alias.'.isMiscellaneous = :isMiscellaneous')
                ->setParameter('isMiscellaneous', 0);

            // the cost_centre_ref = 'IR';
            $qb->andWhere($qb->expr()->eq($this->alias.'.costCentreRef', ':costCentreRef'))
                ->setParameter('costCentreRef', Entity::COST_CENTRE_REF_TYPE_IRFO);
        }

        $application = null;
        $licence = null;
        if ($query->getLicence() !== null) {
            $licence = $this->getReference(LicenceEntity::class, $query->getLicence());
            $trafficArea = $licence->getTrafficArea();
        }

        if ($query->getApplication()) {
            $application = $this->getReference(ApplicationEntity::class, $query->getApplication());
            $trafficArea = $application->getLicence()->getTrafficArea();
        }

        if ($licence || $application) {
            // fee_type records where: is_miscellaneous = 0
            $qb->andWhere($this->alias.'.isMiscellaneous = :isMiscellaneous')
                ->setParameter('isMiscellaneous', 0);

            // the cost_centre_ref NOT = 'IR'
            $qb->andWhere($qb->expr()->neq($this->alias.'.costCentreRef', ':notCostCentreRef'))
                ->setParameter('notCostCentreRef', Entity::COST_CENTRE_REF_TYPE_IRFO);

            // fee_type.good_or_psv = <current operator type>
            $qb->andWhere($qb->expr()->eq($this->alias.'.goodsOrPsv', ':goodsOrPsv'));
            $qb->setParameter('goodsOrPsv', $application ? $application->getGoodsOrPsv() : $licence->getGoodsOrPsv());

            // if it is the application fee page then fee_type.licence_type = <current application licence type>
            // Otherwise where fee_type.licence_type = <current licence type>
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq($this->alias.'.licenceType', ':licenceType'),
                    $qb->expr()->isNull($this->alias.'.licenceType') // OLCS-11129 include NULLs
                )
            );
            $qb->setParameter(
                'licenceType',
                $application ? $application->getLicenceType() : $licence->getLicenceType()
            );

            if ($trafficArea->getIsNi()) {
                // if traffic area is northern_ireland then all fee types where the traffic_centre_id = 'N'
                $qb->andWhere($qb->expr()->eq($this->alias.'.trafficArea', ':trafficArea'));
            } else {
                // Otherwise where the traffic centre code is NOT 'N'.
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->neq($this->alias.'.trafficArea', ':trafficArea'),
                        $qb->expr()->isNull($this->alias.'.trafficArea')
                    )
                );
            }
            $niTrafficArea = $this->getReference(
                TrafficAreaEntity::class,
                TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
            );
            $qb->setParameter('trafficArea', $niTrafficArea);
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param QueryInterface $query
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('feeType', 'ftft');
    }

    /**
     * Get the fee type based on IrfoPsvAuth and fee type string
     *
     * @param IrfoGvPermit|IrfoPsvAuth $irfoEntity
     * @param RefDataEntity $feeTypeFeeType
     * @return Entity
     * @throws NotFoundException
     */
    public function getLatestIrfoFeeType($irfoEntity, RefDataEntity $feeTypeFeeType)
    {
        if ($irfoEntity instanceOf IrfoPsvAuthEntity) {
            $irfoFeeType = $irfoEntity->getIrfoPsvAuthType()->getIrfoFeeType();
        } elseif ($irfoEntity instanceof IrfoGvPermitEntity) {
            $irfoFeeType = $irfoEntity->getIrfoGvPermitType()->getIrfoFeeType();
        } else {
            throw new NotFoundException('Irfo Fee type not found');
        }

        /** @var \Dvsa\Olcs\Api\Domain\Repository\FeeType $feeTypeRepo */
        $feeType = $this->fetchLatestForIrfo(
            $irfoFeeType,
            $feeTypeFeeType
        );

        return $feeType;
    }
}
