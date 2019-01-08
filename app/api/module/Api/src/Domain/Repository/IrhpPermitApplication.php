<?php

/**
 * IrhpPermitApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class IrhpPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getByIrhpApplicationWithStockInfo($irhpApplicationId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('ipa as irhpPermitApplication, ips.validTo as validTo, IDENTITY(ips.country) as countryId')
            ->from(Entity::class, 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->innerJoin('ipw.irhpPermitStock', 'ips')
            ->where('IDENTITY(ipa.irhpApplication) = ?1')
            ->setParameter(1, $irhpApplicationId)
            ->getQuery()
            ->getResult();
    }
}
