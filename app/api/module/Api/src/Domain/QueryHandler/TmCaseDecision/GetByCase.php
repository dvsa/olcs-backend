<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmCaseDecision;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * GetByCase
 */
final class GetByCase extends AbstractQueryHandler
{
    protected $repoServiceName = 'TmCaseDecision';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchLatestUsingCase($query),
            ['rehabMeasures', 'unfitnessReasons']
        );
    }
}
