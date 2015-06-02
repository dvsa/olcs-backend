<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\LegacyOffence;

/**
 * Legacy Offence QueryHandler
 */
final class LegacyOffenceList extends AbstractQueryHandler
{
    protected $repoServiceName = 'LegacyOffence';

    public function handleQuery(QueryInterface $query)
    {
        /** @var LegacyOffence $repo */
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
