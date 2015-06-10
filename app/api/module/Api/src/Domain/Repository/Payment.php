<?php

/**
 * Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Fee\Payment as Entity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Payment extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'p';

    /**
     * @param string $reference
     * @param int $hydrateMode
     * @param null $version
     */
    public function fetchByReference($reference, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('feePayments', 'fp')
            ->with('fp.fee', 'f')
            ->with('f.licence', 'l')
            ->with('f.application')
            ->with('l.organisation');

        $qb
            ->andWhere($qb->expr()->eq('p.guid', ':reference'))
            ->setParameter('reference', $reference);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }

}
