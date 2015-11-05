<?php

/**
 * LicenceDecisions.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * LicenceDecisions
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceDecisions extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result($this->getRepo()->fetchUsingId($query));
    }
}
