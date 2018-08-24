<?php

/**
 * Country
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>, Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Country
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>, Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Country extends AbstractRepository
{
    protected $entity = Entity::class;


    /**
     * Applies filters to list queries. Note we always ignore newly uploaded files until they've been fully submitted
     *
     * @param QueryBuilder   $qb    doctrine query builder
     * @param QueryInterface $query the query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getIsEcmtState') && !empty($query->getIsEcmtState())) {
            $qb->andWhere($qb->expr()->in($this->alias . '.isEcmtState', ':isEcmtState'))
              ->setParameter('isEcmtState', $query->getIsEcmtState());
            $qb->addOrderBy($this->alias.'.countryDesc', 'ASC');
        }
    }




    //TODO review following - couldnt get bundles or applyListJoins to work as replacement

    /**
     * Get all ECMT countries that have constraints
     *
     * @return array
     *
     */
    public function getConstrainedEcmtCountries($array = false)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata();
        $qb->andWhere($qb->expr()->eq($this->alias . '.isEcmtState', ':isEcmtState'))->setParameter('isEcmtState', 1);
        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        $data = array();

        foreach ($results as $row) {
            if ($row->getConstraints() && $row->getConstraints()->count() > 0) {
                if ($array) {
                    $data[] = $row->getId();
                } else {
                    $data[] = array(
                        'id' => $row->getId(),
                        'countryDesc' => $row->getCountryDesc(),
                        'description' => $row->getCountryDesc()
                    );
                }
            }
        }
        return array(count($data),$data);
    }
}
