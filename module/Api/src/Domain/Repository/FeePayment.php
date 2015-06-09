<?php

/**
 * Fee Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Fee\FeePayment as Entity;

/**
 * Fee Payment
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeePayment extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'fp';
}
