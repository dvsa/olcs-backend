<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrfoPermitStock QueryHandler
 */
final class IrfoPermitStockList extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoPermitStock';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
