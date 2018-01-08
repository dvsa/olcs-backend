<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * ProposeToRevokeByCase
 */
final class ProposeToRevokeByCase extends AbstractQueryHandler
{
    protected $repoServiceName = 'ProposeToRevoke';

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var ProposeToRevoke $repo */
        $repo = $this->getRepo();

        // retrieve reason even if deleted
        $repo->disableSoftDeleteable(
            [
                \Dvsa\Olcs\Api\Entity\Pi\Reason::class
            ]
        );

        return $this->result(
            $repo->fetchProposeToRevokeUsingCase($query),
            ['presidingTc', 'reasons', 'assignedCaseworker']
        );
    }
}
