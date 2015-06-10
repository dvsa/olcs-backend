<?php

/**
 * GracePeriod
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\GracePeriod;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * GracePeriod
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriod extends AbstractQueryHandler
{
    protected $repoServiceName = 'GracePeriod';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}
