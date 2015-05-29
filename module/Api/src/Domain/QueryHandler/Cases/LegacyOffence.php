<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PI
 */
final class LegacyOffence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    public function handleQuery(QueryInterface $query)
    {
        $case = $this->getRepo()->fetchById($query->getCase());

        $legacyOffence = $case->getLegacyOffence($query->getOffence());

        return $legacyOffence;
    }
}
