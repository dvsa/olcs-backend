<?php

/**
 * Financial Standing Rate
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;

/**
 * Financial Standing Rate
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialStandingRate extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'fsr';

    /**
     * @param \DateTime $date
     *
     * @return array \Dvsa\Olcs\Api\Entity\System\FinancialStandingRate
     * @throws Exception\NotFoundException
     */
    public function getRatesInEffect(\DateTime $date) {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->withRefdata();

        $effectiveFrom = $date->format(\DateTime::W3C);

        $qb
            ->andWhere($qb->expr()->isNull('fsr.deletedDate'))
            ->andWhere($qb->expr()->lte('fsr.effectiveFrom', ':effectiveFrom'))
            ->addOrderBy('fsr.effectiveFrom', 'DESC')
            ->setParameter('effectiveFrom', $effectiveFrom);

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            throw new Exception\NotFoundException('No effective rates found');
        }

        return $results;
    }
}
