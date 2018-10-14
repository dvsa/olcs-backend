<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IRHP Application
 *
 */
class GetList extends AbstractListQueryHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $bundle = ['licence' => ['organisation']];

    public function handleQuery(QueryInterface $query)
    {
        return parent::handleQuery($query);
    }
}
