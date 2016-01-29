<?php

/**
 * Licence Inspection Request List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Licence Inspection Request List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceInspectionRequestList extends AbstractQueryHandler
{
    protected $repoServiceName = 'InspectionRequest';

    public function handleQuery(QueryInterface $query)
    {
        return [
            'result' => $this->getRepo()->fetchList($query),
            'count' =>   $this->getRepo()->fetchCount($query)
        ];
    }
}
