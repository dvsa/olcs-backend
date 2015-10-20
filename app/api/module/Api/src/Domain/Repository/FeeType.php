<?php

/**
 * Fee Type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as Entity;

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
            $date = new \DateTime();
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
}
