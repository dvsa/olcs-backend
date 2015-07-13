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

/**
 * CompaniesHouseCompany
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseCompany extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'cc';

    public function getLatestByCompanyNumber($number)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->order('createdOn', 'DESC');
        $qb
            ->andWhere($qb->expr()->eq($this->alias . '.companyNumber', ':companyNumber'))
            ->setParameter('companyNumber', $number)
            ->setMaxResults(1);;

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }
}
