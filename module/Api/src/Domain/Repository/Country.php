<?php

/**
 * Country
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Country
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Country extends AbstractRepository
{
    protected $entity = Entity::class;

/**
 * Get all countries that are part of ECMT
 *
 * @return array
 *
 */
    public function getEcmtCountries(){
        $qbs = $this->getEntityManager()->createQueryBuilder()
          ->select('c.id,c.countryDesc')
          ->from(Entity::class,'c')
          ->where('c.isEcmtState = 1');
        return array(count($qbs->getQuery()->execute()),$qbs->getQuery()->execute());
    }
}
