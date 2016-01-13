<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * ProposeToRevokeByCase
 */
final class ProposeToRevokeByCase extends AbstractQueryHandler
{
    protected $repoServiceName = 'ProposeToRevoke';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchProposeToRevokeUsingCase($query),
            ['presidingTc', 'reasons']
        );
    }
}
