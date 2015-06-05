<?php

/**
 * Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Fee\Payment as Entity;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
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

    public function fetchByReference($reference, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata();

        $qb
            ->leftJoin('p.feePayments', 'fp')
            ->leftJoin('fp.fee', 'f')
            ->andWhere($qb->expr()->eq('p.guid', ':reference'))
            ->setParameter('reference', $reference);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        // technically a list endpoint as we're not using id
        return ['result' => $results, 'count' => 1];
    }
}
