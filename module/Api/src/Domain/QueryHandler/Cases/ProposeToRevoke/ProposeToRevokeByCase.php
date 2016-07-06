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
        $repo = $this->getRepo();

        // retrieve reason even if deleted
        $repo->disableSoftDeleteable(
            [
                \Dvsa\Olcs\Api\Entity\Pi\Reason::class
            ]
        );

        return $this->result(
            $repo->fetchProposeToRevokeUsingCase($query),
            ['presidingTc', 'reasons']
        );
    }
}
