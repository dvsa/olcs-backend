<?php

/**
 * IrhpPermitApplication
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;

class IrhpPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getByIrhpApplicationWithStockInfo($irhpApplicationId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select(
                'ipa as irhpPermitApplication, ips.validTo as validTo, ips.id as stockId, IDENTITY(ips.country) as countryId'
            )
            ->from(Entity::class, 'ipa')
            ->innerJoin('ipa.irhpPermitWindow', 'ipw')
            ->innerJoin('ipw.irhpPermitStock', 'ips')
            ->where('IDENTITY(ipa.irhpApplication) = ?1')
            ->setParameter(1, $irhpApplicationId)
            ->getQuery()
            ->getResult();
    }
}
