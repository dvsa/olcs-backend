<?php

/**
 * IrfoCountry List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrfoCountry List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class IrfoCountryList extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoCountry';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
