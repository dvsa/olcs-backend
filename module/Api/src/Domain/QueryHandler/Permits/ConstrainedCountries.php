<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Constrained Countries
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class ConstrainedCountries extends AbstractQueryHandler
{
    protected $repoServiceName = 'ConstrainedCountries';

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        $constrainedCountries = $repo->fetchList($query, Query::HYDRATE_OBJECT);

        $data = array();
        foreach ($constrainedCountries as $country)
        {

            $data[] = array(
              'id'          => $country->getCountry()->getId(),
              'description' => $country->getCountry()->getCountryDesc(),
              'constraint'  => array(
                'id'          => $country->getConstraint()->getId(),
                'name'        => $country->getConstraint()->getName(),
                'description' => $country->getConstraint()->getDescription()
              )
            );
        }

        $count = $repo->fetchCount($query);

        //TODO handle what happens if a country has more than one constraint

        return [
          'count' => $count,
          'result' => $data
        ];
    }
}
