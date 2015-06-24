<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Scan;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * GetSingle
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetSingle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Scan';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result($this->getRepo()->fetchUsingId($query));
    }
}
