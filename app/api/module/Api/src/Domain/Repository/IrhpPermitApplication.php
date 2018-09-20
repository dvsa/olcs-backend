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

/**
 * IrhpPermitApplication
 */
class IrhpPermitApplication extends AbstractRepository
{
    protected $entity = Entity::class;

    public function getIrhpPermitApplicationsForScoring(int $irhpPermitStockId, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->getEntityManager()->createQueryBuilder()
                ->select('ipa')
                ->from(Entity::class, 'ipa')
                ->innerJoin('ipa.irhpPermitWindow', 'ipw')
                ->innerJoin('ipw.irhpPermitStock', 'ips')
                ->innerJoin('ipa.licence', 'l')
                ->where('ips.id = ?1')
                ->andWhere('ipa.status = \'ecmt_permit_uc\'')
                ->andWhere('l.licenceType IN (\'ltyp_r\', \'ltyp_si\')')
                ->setParameter(1, $irhpPermitStockId)
                ->getQuery()
                ->getResult($hydrateMode);
    }
}
