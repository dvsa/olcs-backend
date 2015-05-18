<?php

/**
 * FeeType
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

/**
 * FeeType
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class FeeType extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Fee\FeeType';

    protected $alias = 'ft';

    /**
     * @param RefDataEntity $feeType
     * @param RefDataEntity $goodsOrPsv
     * @param RefDataEntity $licenceType
     * @param \DateTime $date
     * @param TrafficArea $trafficArea
     *
     * @return \Dvsa\Olcs\Api\Entity\Fee\FeeType
     * @throws Exception\NotFoundException
     */
    public function fetchLatest(
        RefDataEntity $feeType,
        RefDataEntity $goodsOrPsv,
        RefDataEntity $licenceType,
        \DateTime $date,
        $trafficArea = null
    ) {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->withRefdata();

        $effectiveFrom = $date->format(\DateTime::W3C);

        $qb->andWhere($qb->expr()->eq('ft.feeType', ':feeType'))
            ->andWhere($qb->expr()->eq('ft.goodsOrPsv', ':goodsOrPsv'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ft.licenceType', ':licenceType'),
                    $qb->expr()->isNull('ft.licenceType')
                )
            )
            ->andWhere($qb->expr()->lte('ft.effectiveFrom', ':effectiveFrom'));

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
            ->setParameter('effectiveFrom', $effectiveFrom)
            ->setMaxResults(1);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            throw new Exception\NotFoundException('FeeType not found');
        }

        return $results[0];
    }
}
