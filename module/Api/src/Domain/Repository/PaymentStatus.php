<?php

/**
 * Payment status
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\PaymentStatus as Entity;

/**
 * Payment status
 */
class PaymentStatus extends AbstractRepository
{
    protected $entity = Entity::class;
}
