<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\EcmtPermits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SiftingSettings
 */
class SiftingSettings extends AbstractQueryHandler
{
    protected $repoServiceName = 'SiftingSettings';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchUsingId($query)
        );
    }
}
