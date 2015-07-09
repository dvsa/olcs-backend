<?php

/**
 * CompaniesHouseCompany
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * CompaniesHouseCompany
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseCompany extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'cc';
}
