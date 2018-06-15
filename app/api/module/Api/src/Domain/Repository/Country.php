<?php

/**
 * Country
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as Entity;


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

        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata();
        $qb->andWhere($qb->expr()->eq($this->alias . '.isEcmtState', ':isEcmtState'))->setParameter('isEcmtState', 1);
        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        return array(count($results),$results);
    }

    /**
     * Get all ECMT countries that have constraints
     *
     * @return array
     *
     */
    public function getConstrainedEcmtCountries(){

        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata();
        $qb->andWhere($qb->expr()->eq($this->alias . '.isEcmtState', ':isEcmtState'))->setParameter('isEcmtState', 1);
        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        $data = array();

        foreach ($results as $row){
            if($row->getConstraints()->count() > 0){
                $data[] = array(
                  'id' => $row->getId(),
                  'description' => $row->getCountryDesc()
                );
            }
        }
        return array(count($data),$data);
    }
}
