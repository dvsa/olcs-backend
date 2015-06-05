<?php

/**
 * Irfo Gv Permit
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Irfo Gv Permit
 */
class IrfoGvPermit extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoGvPermit';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}
