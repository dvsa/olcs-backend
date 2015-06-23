<?php

/**
 * LicenceDecisions.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\LicenceStatusRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * LicenceStatusRule
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceStatusRule extends AbstractQueryHandler
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}
