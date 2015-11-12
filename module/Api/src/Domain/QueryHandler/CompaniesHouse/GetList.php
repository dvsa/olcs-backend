<?php

/**
 * Companies house / GetList
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\CompaniesHouseAwareTrait;
use Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface;

/**
 * Companies house / GetList
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler implements CompaniesHouseAwareInterface
{
    use CompaniesHouseAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $result = $this->getCompaniesList($query->getType(), $query->getValue());
        return [
            'result' => $result['Results'],
            'count' => $result['Count'],
            'count-unfiltered' => $result['Count']
        ];
    }
}
