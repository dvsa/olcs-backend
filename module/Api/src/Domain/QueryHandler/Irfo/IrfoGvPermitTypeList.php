<?php

/**
 * IrfoGvPermitType list
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrfoGvPermitType list
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class IrfoGvPermitTypeList extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrfoGvPermitType';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
