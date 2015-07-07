<?php

/**
 * CompaniesHouseAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * CompaniesHouseAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlert extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'ca';
}
