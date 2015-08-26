<?php

/**
 * Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Fee\Transaction as Entity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Transaction
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Transaction extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 't';

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
            ->with('feeTransactions', 'ft')
            ->with('ft.fee', 'f')
            ->with('f.licence', 'l')
            ->with('f.application')
            ->with('l.organisation');

        $qb
            ->andWhere($qb->expr()->eq('t.reference', ':reference'))
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
