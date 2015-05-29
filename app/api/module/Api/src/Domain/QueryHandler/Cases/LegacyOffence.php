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
    protected $repoServiceName = 'LegacyOffence';

    public function handleQuery(QueryInterface $query)
    {
        $legacyOffence = $this->getRepo()->fetchCaseLegacyOffenceUsingId($query);

        return $legacyOffence;
    }
}
